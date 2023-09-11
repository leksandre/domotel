<?php

declare(strict_types=1);

namespace Kelnik\Core\Map\Yandex;

use Illuminate\Support\Collection;
use Kelnik\Core\Map\Contracts\Coords;
use Kelnik\Core\Map\Contracts\Marker;
use Kelnik\Core\Map\Enums\MobileDragMode;

final class Map implements \Kelnik\Core\Map\Contracts\Map
{
    private ?string $apiKey = null;
    private Coords $center;
    private int $zoom = 14;
    private int $zoomMargin = 20;
    private string $lang = 'ru';
    private Collection $markers;
    private Collection $routes;
    private ?MobileDragMode $dragMode = null;

    public function __construct(array $config)
    {
        $this->markers = collect();
        $this->routes = collect();

        if (is_string($config['center'])) {
            $config['center'] = explode(',', $config['center']);
        }

        $this->center = $config['center'] instanceof Coords
                        ? $config['center']
                        : resolve(Coords::class, [
                            'lat' => (float)$config['center'][0],
                            'lng' => (float)$config['center'][1]
                        ]);

        if (isset($config['zoom'])) {
            $this->zoom = (int) $config['zoom'];
        }

        if (isset($config['lang'])) {
            $this->lang = (string) $config['lang'];
        }

        if (isset($config['apiKey'])) {
            $this->apiKey = (string) $config['apiKey'];
        }

        if (!empty($config['dragMode']) && $config['dragMode'] instanceof MobileDragMode) {
            $this->dragMode = $config['dragMode'];
        }
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getCenter(): Coords
    {
        return $this->center;
    }

    public function setCenter(Coords $coords): void
    {
        $this->center = $coords;
    }

    public function getZoom(): int
    {
        return $this->zoom;
    }

    public function addMarker(Marker $marker): void
    {
        $this->markers->add($marker);
    }

    public function getMarkers(): Collection
    {
        return $this->markers;
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function toArray(): array
    {
        $res = [
            'zoom' => $this->zoom,
            'zoomMargin' => $this->zoomMargin,
            'center' => $this->center->toArray(),
            'disableMobileDrag' => $this->dragMode === MobileDragMode::Double
        ];

        $markers = [];
        foreach ($this->markers as $marker) {
            if (!$marker->getIcon()) {
                continue;
            }
            $markers[] = $marker->toArray();
        }

        if ($markers) {
            $res['markers'] = $markers;
        }

        $routes = $this->routes->toArray();
        if ($routes) {
            $res['routes'] = $routes;
        }

        return $res;
    }
}
