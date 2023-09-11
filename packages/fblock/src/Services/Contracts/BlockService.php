<?php

namespace Kelnik\FBlock\Services\Contracts;

use Closure;
use Illuminate\Support\Collection;

interface BlockService
{
    public function getBlockList(): Collection;

    public function prepareElements(Collection $res, ?Closure $callback = null): Collection;

    public function getCacheTag(): ?string;
}
