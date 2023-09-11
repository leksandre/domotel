<?php

declare(strict_types=1);

namespace Kelnik\Core\Map\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface Map extends Arrayable, MapMarkers
{
    /**
     * @param array $config
     *
     * Examples of $config
     * [
     *      // Center of map
     *      // string: '59.829133,30.543890'
     *      // array: [59.829133, 30.543890]
     *      // object: new Coords(59.829133, 30.543890)
     *      'center' => '59.829133,30.543890',
     *
     *      'zoom' => 10,
     *
     *      'lang' => 'ru',
     *
     *      'apiKey' => '...'
     * ]
     * @see Coords
     */
    public function __construct(array $config);

    public function getApiKey(): string;

    public function getCenter(): Coords;

    public function setCenter(Coords $coords);

    public function getZoom(): int;

    public function getLang(): string;
}
