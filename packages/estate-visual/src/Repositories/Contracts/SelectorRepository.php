<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Contracts;

use Kelnik\EstateVisual\Models\Selector;

interface SelectorRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): Selector;

    public function getActiveFirst(): Selector;
}
