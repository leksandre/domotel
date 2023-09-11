<?php

declare(strict_types=1);

namespace Kelnik\Document\Repositories\Contracts;

use Kelnik\Document\Models\Element;

interface ElementRepository extends BaseRepository
{
    public function isUnique(Element $model): bool;

    public function findByPrimary(int|string $primary): Element;
}
