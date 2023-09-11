<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

interface AdminAutoFilter
{
    public function getAllAutoFilteredForAdmin(): Collection;

    public function getAllAutoFilteredForAdminPaginated(): Paginator;
}
