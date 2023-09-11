<?php

declare(strict_types=1);

namespace Kelnik\Contact\Repositories\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Contact\Models\Office;

interface OfficeRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): Office;

    public function getAll(): Collection;
}
