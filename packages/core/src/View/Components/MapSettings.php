<?php

declare(strict_types=1);

namespace Kelnik\Core\View\Components;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Illuminate\View\View;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;
use Kelnik\Core\View\Components\Contracts\KelnikComponentCache;

final class MapSettings extends Component implements KelnikComponentAlias, KelnikComponentCache
{
    public function __construct(private readonly SettingsService $settingsService)
    {
    }

    public static function getAlias(): string
    {
        return 'kelnik-core-map-settings';
    }

    private function getTemplateData()
    {
        $cacheId = self::getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $map = resolve(SettingsRepository::class)->get(
            CoreServiceProvider::MODULE_NAME,
            $this->settingsService::PARAM_MAP
        )?->value ?? new Collection();

        Cache::tags([
            $this->settingsService->getCacheTag(
                CoreServiceProvider::MODULE_NAME,
                $this->settingsService::PARAM_MAP
            )
        ])->forever($cacheId, $map);

        return $map;
    }

    public function render(): View|string|null
    {
        return view('kelnik-core::components.map-settings', $this->getTemplateData()->toArray());
    }

    public function getCacheId(): string
    {
        return 'mapSettings';
    }
}
