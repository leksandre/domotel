<?php

declare(strict_types=1);

namespace Kelnik\Core\Services;

use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Map\Enums\MobileDragMode;
use Kelnik\Core\Models\Setting;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Theme\Color;
use Kelnik\Core\Theme\Font;

final class SettingsService implements Contracts\SettingsService
{
    public function __construct(
        private readonly SettingsRepository $repository,
        private readonly AttachmentRepository $attachmentRepository
    ) {
    }

    public function getDefaultColors(array $colorParams = []): array
    {
        $res = [];
        $srcPath = config('kelnik-core.theme.scssPath');

        // @codeCoverageIgnoreStart
        if (!$srcPath || !is_file($srcPath) || !is_readable($srcPath)) {
            return $res;
        }
        // @codeCoverageIgnoreEnd

        if (!$colorParams) {
            $colorParams = $this->getBaseColorNames();
        }

        $fp = fopen($srcPath, 'r');
        $paramPrefix = config('kelnik-core.theme.brandPrefix');
        $reg = '!\\' . $paramPrefix . '([a-z\d\-_]+):\s*(#[a-f\d]{3,6});!si';

        while (!feof($fp)) {
            $str = fgets($fp, 4096);

            if (!is_string($str)) {
                continue;
            }

            $str = trim($str);
            if (!$str || stripos($str, $paramPrefix) !== 0) {
                continue;
            }

            preg_match($reg, $str, $match);

            if (empty($match[1]) || empty($match[2]) || !in_array($match[1], $colorParams)) {
                continue;
            }

            $res[strtolower($match[1])] = strtolower($match[2]);
        }
        fclose($fp);

        return $res;
    }

    public function getCurrentColors(array $colorParams = []): array
    {
        $defColors = $this->getDefaultColors($colorParams);

        $colorsFromSettings = $this->repository->get(CoreServiceProvider::MODULE_NAME, self::PARAM_COLORS);
        $colorsFromSettings = $colorsFromSettings?->value->toArray();

        return $colorsFromSettings
            ? array_merge($defColors, $colorsFromSettings)
            : $defColors;
    }

    public function getDefaultFonts(): array
    {
        return [
            'regular' => [],
            'bold' => []
        ];
    }

    public function getComplex(): Collection
    {
        return $this->getCached(
            CoreServiceProvider::MODULE_NAME,
            self::PARAM_COMPLEX
        )?->value ?? new Collection();
    }

    public function getBaseColorNames(): array
    {
        return config('kelnik-core.theme.colors.brand');
    }

    public function getComponentColorNames(): array
    {
        return config('kelnik-core.theme.colors.component');
    }

    public function getAllColorNames(): array
    {
        return array_merge($this->getBaseColorNames(), $this->getComponentColorNames());
    }

    public function prepareColors(Collection $colors, ?Collection $defColors = null): Collection
    {
        return $colors->map(static function ($color, $colorName) use ($defColors) {
            if (!$color instanceof \Kelnik\Core\Theme\Contracts\Color) {
                $color = new Color($colorName, $color);
            }

            if ($defColors && isset($defColors[$color->getName()]) && !$color->getDefaultValue()) {
                $color->setDefaultValue($defColors[$color->getName()]);
            }

            return $color;
        });
    }

    public function prepareFonts(Collection $fonts): Collection
    {
        $files = $fonts->pluck('file')->filter()->toArray();

        if ($files) {
            $files = $this->attachmentRepository->getByPrimary($files) ?? new Collection();
        }

        return $fonts->map(static function ($el) use ($files) {
            if ($el instanceof \Kelnik\Core\Theme\Contracts\Font) {
                return $el;
            }

            if ($files) {
                $el['file'] = isset($el['file'])
                    ? $files->first(static fn($attach) => $attach->id === $el['file'])
                    : null;
            }

            return new Font($el['file'] ?? null, $el['active'] ?? false, $el['title'] ?? null);
        });
    }

    public function getMapDragModeDefault(): MobileDragMode
    {
        $cases = MobileDragMode::cases();

        foreach ($cases as $el) {
            if ($el->isDefault()) {
                return $el;
            }
        }

        return current($cases);
    }

    public function getCached(
        string $moduleName,
        string|array $settingName,
        DateTimeInterface|DateInterval|int $cacheTtl = 0
    ): Setting|Collection {
        $returnArray = true;

        if (!is_array($settingName)) {
            $returnArray = false;
            $settingName = [$settingName];
        }

        $cached = collect();

        foreach ($settingName as $v) {
            $cacheId = $this->getCacheId($moduleName, $v);
            $cache = Cache::get($cacheId, false);
            if ($cache) {
                $cached->add($cache);
            }
        }

        $cachedNames = $cached->pluck('name')->toArray();
        $requiredNames = array_diff($settingName, $cachedNames);

        if (!$requiredNames) {
            return $returnArray ? $cached : $cached->first();
        }

        $res = $this->repository->get($moduleName, $requiredNames);

        if ($cached->isNotEmpty()) {
            $res = $res->mergeRecursive($cached);
        }

        if ($res->isEmpty()) {
            return $returnArray ? $res : new Setting();
        }

        $cache = $cacheTtl === 0
            ? static fn($key, $val) => Cache::forever($key, $val)
            : static fn($key, $val) => Cache::put($key, $val, $cacheTtl);

        $res->each(function (Setting $setting) use ($cache, $cachedNames) {
            if (in_array($setting->name, $cachedNames)) {
                return;
            }
            $cache(
                $this->getCacheId($setting->module, $setting->name),
                $setting
            );
        });

        return $returnArray ? $res : $res->first();
    }

    public function resetSettingCache(string $module, string $name): bool
    {
        return Cache::forget($this->getCacheId($module, $name))
            && Cache::tags(self::getCacheTag($module, $name))->flush();
    }

    protected function getCacheId(string $module, string $name): string
    {
        return strtolower('setting_' . $module . '_' . $name);
    }

    public function getCacheTag(string $module, string $name): string
    {
        return 'settingTag_' . $module . '_' . $name;
    }
}
