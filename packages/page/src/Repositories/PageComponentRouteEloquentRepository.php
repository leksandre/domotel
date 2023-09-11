<?php

declare(strict_types=1);

namespace Kelnik\Page\Repositories;

use Illuminate\Support\Collection;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Repositories\Contracts\PageComponentRouteRepository;

final class PageComponentRouteEloquentRepository implements PageComponentRouteRepository
{
    /** @var class-string $model */
    private $model = PageComponentRoute::class;

    public function findByPrimary(int|string $primary): PageComponentRoute
    {
        return $this->model::findOrNew($primary);
    }

    public function getByPageComponentKey(int|string $pageComponentKey): Collection
    {
        return $this->model::where('page_component_id', $pageComponentKey)->firstOrNew();
    }

    public function save(PageComponentRoute $pageComponentRoute): bool
    {
        return $pageComponentRoute->save();
    }

    public function delete(PageComponentRoute $pageComponentRoute): bool
    {
        return $pageComponentRoute->delete();
    }
}
