<?php

declare(strict_types=1);

namespace Kelnik\Page\Repositories\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Page\Models\PageComponentRoute;

interface PageComponentRouteRepository
{
    public function findByPrimary(int|string $primary): PageComponentRoute;

    public function getByPageComponentKey(int|string $pageComponentKey): Collection;

    public function save(PageComponentRoute $pageComponentRoute): bool;

    public function delete(PageComponentRoute $pageComponentRoute): bool;
}
