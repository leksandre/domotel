<?php

declare(strict_types=1);

namespace Kelnik\Page\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Models\PageComponentRouteElement;
use Kelnik\Page\Repositories\Contracts\PageComponentRouteElementRepository;

final class PageComponentRouteElementEloquentRepository implements PageComponentRouteElementRepository
{
    /** @var class-string $model */
    private $model = PageComponentRouteElement::class;

    public function findByPrimary(int|string $primary): PageComponentRouteElement
    {
        return $this->model::findOrNew($primary);
    }

    public function getByModelAndElementId(
        string $modelNameSpace,
        int|string $elementId
    ): Collection {
        return $this->model::where('model_name', $modelNameSpace)
            ->where('element_id', $elementId)
            ->with([
                'pageComponentRoute' => fn(BelongsTo $belongsTo) => $belongsTo->select('id', 'page_component_id')
                    ->with([
                        'pageComponent' => fn(BelongsTo $belongsTo) => $belongsTo->select(['id', 'page_id'])
                            ->with(['page' => fn(BelongsTo $belongsTo) => $belongsTo->select(['id', 'site_id'])])
                    ])
            ])
            ->get();
    }

    public function getByModelElements(array $modelElements): Collection
    {
        if (!$modelElements) {
            return new Collection();
        }

        $query = $this->model::query()->with([
            'pageComponentRoute' => fn(BelongsTo $belongsTo) => $belongsTo->select('id', 'page_component_id')
                ->with([
                    'pageComponent' => fn(BelongsTo $belongsTo) => $belongsTo->select(['id', 'page_id'])
                        ->with(['page' => fn(BelongsTo $belongsTo) => $belongsTo->select(['id', 'site_id'])])
                ])
        ]);

        foreach ($modelElements as $modelName => $ids) {
            $query->orWhere(function (Builder $builder) use ($modelName, $ids) {
                $builder->where('model_name', $modelName)
                    ->when($ids, fn(Builder $builder) => $builder->whereIn('element_id', $ids));
            });
        }

        return $query->get();
    }

    public function getByModule(string $moduleName): Collection
    {
        return $this->model::where('module', $moduleName)->get();
    }

    public function save(PageComponentRouteElement $componentRouteModuleElement): bool
    {
        return $componentRouteModuleElement->save();
    }

    public function delete(PageComponentRouteElement $componentRouteModuleElement): bool
    {
        return $componentRouteModuleElement->delete();
    }

    public function deleteByClassNameAndElement(string $className, int|string $elementId): int
    {
        return $this->model::where('element_id', $elementId)->where('model_name', $className)->delete();
    }

    public function deleteBySiteClassNameAndElement(int|string $siteId, string $className, int|string $elementId): int
    {
        return $this->model::query()
            ->whereIn(
                'page_component_route_id',
                PageComponentRoute::query()
                    ->select(['id'])
                    ->whereHas(
                        'page',
                        static fn(Builder $builder) => $builder->select('pages.id')
                            ->where('pages.site_id', $siteId)
                    )
            )
            ->where('element_id', $elementId)
            ->where('model_name', $className)
            ->delete();
    }

    public function deleteByModule(string $moduleName): int
    {
        return $this->model::query()->where('module_name', '=', $moduleName)->delete();
    }
}
