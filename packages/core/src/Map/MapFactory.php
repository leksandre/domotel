<?php

declare(strict_types=1);

namespace Kelnik\Core\Map;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Kelnik\Core\Map\Contracts\Map;
use Kelnik\Core\Map\Contracts\Marker;
use Kelnik\Core\Map\Enums\MobileDragMode;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Services\Contracts\SettingsService;

final class MapFactory
{
    private readonly SettingsService $settingsService;

    public function __construct()
    {
        $this->settingsService = resolve(SettingsService::class);
    }

    public function makeMap(array $config = [], ?string $serviceName = null): Map
    {
        $serviceName ??= self::getServiceName();
        $config = array_merge(self::getMapSettings(), $config ?? []);
        $serviceMapClass = config('kelnik-core.map.services.' . $serviceName . '.map');

        if (!$serviceMapClass || !class_exists($serviceMapClass)) {
            throw new InvalidArgumentException('Service `' . $serviceName . '` not available');
        }

        return new $serviceMapClass($config);
    }

    public function makeMarker(array $data, ?string $serviceName = null): Marker
    {
        $serviceName ??= $this->getServiceName();
        $serviceMarkerClass = config('kelnik-core.map.services.' . $serviceName . '.marker');

        if (!$serviceMarkerClass || !class_exists($serviceMarkerClass)) {
            throw new InvalidArgumentException('Service `' . $serviceName . '` not available');
        }

        return new $serviceMarkerClass($data);
    }

    private function getServiceName(): string
    {
        return $this->getMapGlobalSettings()->get('service') ?? config('kelnik-core.map.service');
    }

    private function getMapSettings(): array
    {
        $mapGlobalSettings = $this->getMapGlobalSettings();
        $serviceName = $this->getServiceName();
        $serviceData = $mapGlobalSettings->get($serviceName);

        return [
            'center' => config('kelnik-core.map.center'),
            'zoom' => config('kelnik-core.map.zoom'),
            'lang' => app()->getLocale(),
            'apiKey' => $serviceData['api'] ?? null,
            'dragMode' => MobileDragMode::tryFrom($mapGlobalSettings->get('dragMode') ?? '')
                ?? $this->settingsService->getMapDragModeDefault()
        ];
    }

    private function getMapGlobalSettings(): Collection
    {
        return $this->settingsService->getCached(
            CoreServiceProvider::MODULE_NAME,
            $this->settingsService::PARAM_MAP
        )?->value ?? new Collection();
    }
}
