<?php

declare(strict_types=1);

namespace Kelnik\Core\Map\Contracts;

use Illuminate\Support\Collection;

interface MapMarkers
{
    public function addMarker(Marker $marker);

    /** @return Collection<Marker> */
    public function getMarkers(): Collection;
}
