<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AdminBaseRepository
{
    public function getAllForAdmin(): Collection;

    public function getAllForAdminPaginated(): LengthAwarePaginator;

    public function getByPrimaryForAdmin(iterable $primaryKeys): Collection;
}
