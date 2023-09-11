<?php

declare(strict_types=1);

namespace Kelnik\Progress\Repositories\Contracts;

use Illuminate\Support\Collection;

interface CameraRepository extends BaseRepository
{
    public function getAdminList(): Collection;

    public function getAll(): Collection;

    public function getActive(?int $group = null): Collection;
}
