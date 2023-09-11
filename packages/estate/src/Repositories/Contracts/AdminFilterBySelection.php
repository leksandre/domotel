<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

interface AdminFilterBySelection
{
    public function getAllBySelectionForAdmin(string $selectionClassName): Collection;

    public function getAllBySelectionForAdminPaginated(string $selectionClassName): Paginator;
}
