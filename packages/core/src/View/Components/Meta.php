<?php

declare(strict_types=1);

namespace Kelnik\Core\View\Components;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Illuminate\View\View;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;
use Kelnik\Core\View\Components\Contracts\KelnikComponentCache;

final class Meta extends Component implements KelnikComponentAlias, KelnikComponentCache
{
    private const COLOR_NAME = 'brand-base';
    private const COLOR_DEFAULT = '#000000';

    public function __construct(private readonly SettingsService $settingsService)
    {
    }

    public static function getAlias(): string
    {
        return 'kelnik-core-meta';
    }

    private function getTemplateData()
    {
        $cacheId = self::getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $res = [];
        $cacheTags = [
            $this->settingsService->getCacheTag(
                CoreServiceProvider::MODULE_NAME,
                $this->settingsService::PARAM_COMPLEX
            ),
            $this->settingsService->getCacheTag(
                CoreServiceProvider::MODULE_NAME,
                $this->settingsService::PARAM_COLORS
            )
        ];

        $settings = $this->settingsService->getCached(
            CoreServiceProvider::MODULE_NAME,
            [$this->settingsService::PARAM_COMPLEX, $this->settingsService::PARAM_COLORS]
        );

        if ($settings->isEmpty()) {
            $res['color'] = $this->prepareColors(new Collection())->get(self::COLOR_NAME)?->getValue();
            Cache::tags($cacheTags)->forever($cacheId, $res);

            return $res;
        }

        $complex = $settings->first(fn($el) => $el->name === $this->settingsService::PARAM_COMPLEX)?->value
            ?? new Collection();
        $colors = $settings->first(fn($el) => $el->name === $this->settingsService::PARAM_COLORS)?->value
            ?? new Collection();

        unset($settings);

        $colors = $this->prepareColors($colors);

        $res = [
            'name' => $complex->get('name') ?? '',
            'color' => $colors->get(self::COLOR_NAME)?->getValue()
        ];

        Cache::tags($cacheTags)->forever($cacheId, $res);

        return $res;
    }

    public function render(): View|string|null
    {
        $data = $this->getTemplateData();
        $data['color'] ??= self::COLOR_DEFAULT;

        return view('kelnik-core::components.meta', $data);
    }

    public function getCacheId(): string
    {
        return 'meta';
    }

    private function prepareColors(Collection $colors): Collection
    {
        return $this->settingsService->prepareColors(
            $colors->has(self::COLOR_NAME)
                ? $colors
                : new Collection($this->settingsService->getDefaultColors([self::COLOR_NAME]))
        );
    }
}
