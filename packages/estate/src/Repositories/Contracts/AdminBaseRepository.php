<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Support\Collection;

interface AdminBaseRepository
{
    public function getAllForAdmin(): Collection;

    public function getByPrimaryForAdmin(iterable $primaryKeys): Collection;
}
