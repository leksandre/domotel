<?php

declare(strict_types=1);

namespace Kelnik\Page\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Repositories\Contracts\PageComponentRepository;

final class PageComponentEloquentRepository implements PageComponentRepository
{
    /** @var class-string $model */
    private $model = PageComponent::class;

    public function getActiveComponentsByClassName(string|array $className): Collection
    {
        if (!is_array($className)) {
            $className = [$className];
        }

        return $this->model::query()
            ->with(['page' => static fn(BelongsTo $query) => $query->select(['id', 'parent_id', 'active'])])
            ->whereHas(
                'page',
                static fn(Builder $query) => $query->select(['id'])->active()->limit(1)
            )
            ->where('active', true)
            ->whereIn('component', $className)
            ->get();
    }

    public function getComponentsByPage(int|string $siteId, int|string $pageId): Collection
    {
        return $this->model::query()->orderBy('priority')->get();
    }

    public function getPageDynamicComponentFirstRoute(int $pageId): ?PageComponentRoute
    {
        /** @var BelongsTo $componentRelation */
        $model = resolve(PageComponentRoute::class);
        $componentRelation = $model->pageComponent();
        $tableName = $model->getTable();

        return PageComponentRoute::query()
            ->select([
                $tableName . '.id',
                $tableName . '.' . $componentRelation->getRelated()->getForeignKey()
            ])
            ->join(
                $componentRelation->getRelated()->getTable(),
                $componentRelation->getQualifiedOwnerKeyName(),
                '=',
                $componentRelation->getQualifiedForeignKeyName()
            )
            ->where($componentRelation->getRelated()->getTable() . '.page_id', '=', $pageId)
            ->limit(1)
            ->firstOrNew();
    }

    public function findByPrimary(int|string $primary): PageComponent
    {
        return $this->model::findOrNew($primary);
    }

    public function save(PageComponent $pageComponent): bool
    {
        return $pageComponent->save();
    }

    public function delete(PageComponent $pageComponent): bool
    {
        return $pageComponent->delete();
    }
}
