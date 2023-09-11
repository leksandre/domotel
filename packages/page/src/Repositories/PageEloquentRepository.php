<?php

declare(strict_types=1);

namespace Kelnik\Page\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Kelnik\Page\Models\Enums\Type;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\Repositories\Contracts\PageRepository;
use Throwable;

final class PageEloquentRepository implements PageRepository
{
    /** @var class-string $model */
    private $model = Page::class;

    public function pageIsUnique(Page $page): bool
    {
        $query = $this->model::where('site_id', $page->site_id)
            ->where('parent_id', $page->parent_id)
            ->where('slug', $page->slug)
            ->limit(1);

        if ($page->exists) {
            $query->whereKeyNot($page->id);
        }

        return $query->get('id')->count() === 0;
    }

    public function getActivePageByPrimary(int|string $primary, int|string $siteId): Page
    {
        return $this->model::whereKey($primary)
                    ->where('site_id', $siteId)
                    ->where('active', true)
                    ->where('type', Type::Simple->value)
                    ->firstOrNew();
    }

    public function getPageByPath(string $urlPath, int|string $siteId): Page
    {
        $urlPath = trim($urlPath, '/');
        $urlPath = md5($urlPath);

        return $this->model::where('site_id', $siteId)
            ->where('active', true)
            ->where('type', Type::Simple->value)
            ->where('path', $urlPath)
            ->firstOrNew();
    }

    public function getErrorPage(int|string $siteId): Page
    {
        return $this->model::where('site_id', $siteId)
            ->where('type', Type::Error->value)
            ->firstOrNew();
    }

    public function getActivePageRoutes(bool $checkTable, $cacheId): Collection
    {
        try {
            if ($checkTable && !$this->tableIsCorrect()) {
                return new Collection();
            }
        } catch (Throwable $e) {
            return new Collection();
        }

        return Cache::rememberForever(
            $cacheId,
            fn() => $this->model::query()
                ->select(['id', 'parent_id', 'site_id', 'slug'])
                ->with([
                    'parentNested' => static fn(BelongsTo $query) => $query->select(
                        ['id', 'parent_id', 'site_id', 'slug']
                    ),
                    'activeComponents' => static function (HasMany $query) {
                        $query->select(['id', 'page_id', 'component'])
                            ->with('routes')
                            ->whereHas('routes', static fn(Builder $query) => $query->select(['id'])->limit(1));
                    }
                ])
                ->where('active', true)
                ->where('type', Type::Simple->value)
                ->get()
        );
    }

    private function tableIsCorrect(): bool
    {
        $tableName = (new Page())->getTable();

        return Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'site_id');
    }

    public function getAdminListBySite(int|string $siteId): LengthAwarePaginator
    {
        return $this->model::where('site_id', $siteId)
            ->with([
                'components' => static fn($builder) => $builder->with(['routes'])
            ])
            ->orderBy('priority')
            ->orderBy('id')
            ->paginate();
    }

    public function getPagesWithDynamicComponent(array $componentNamespace, array $siteId = []): Collection
    {
        return !$componentNamespace
            ? new Collection()
            : $this->model::select(['id', 'site_id', 'title'])
                ->when(
                    $siteId,
                    static fn(Builder $query) => $query->whereIn('site_id', $siteId)
                )
                ->where('type', Type::Simple->value)
                ->orderBy('parent_id')
                ->orderBy('priority')
                ->orderBy('title')
                ->whereIn(
                    'id',
                    PageComponent::query()->select('page_id')->whereIn('component', $componentNamespace)
                )->get();
    }

    public function getPagesWithoutDynamicComponents(int|string $siteId = null): Collection
    {
        $query = $this->model::select(['id', 'site_id', 'title'])
            ->when($siteId !== null, fn(Builder $builder) => $builder->where('site_id', $siteId))
            ->where('type', Type::Simple->value)
            ->orderBy('site_id')
            ->orderBy('parent_id')
            ->orderBy('priority')
            ->orderBy('title');

        /** @var Collection $dynamicComponents */
        $dynamicComponents = resolve(BladeComponentRepository::class)->getDynamicComponents();

        if ($dynamicComponents) {
            $query->whereNotIn(
                'id',
                PageComponent::query()
                    ->select('page_id')
                    ->whereIn('component', $dynamicComponents->pluck(null)->toArray())
            );
        }

        return $query->get();
    }

    public function findPrimaryPagesBySite(int|string $siteId): Collection
    {
        return $this->model::where('site_id', $siteId)->where('parent_id', 0)->get();
    }

    public function addComponent(Page $page, PageComponent $pageComponent): bool
    {
        return $page->components()->save($pageComponent) !== false;
    }

    public function findByPrimary(int|string $primary): Page
    {
        return $this->model::findOrNew($primary);
    }

    public function findBySlugAndSiteWithComponent(
        string $slug,
        int|string $siteId,
        string $componentClassName
    ): Page {
        return $this->model::where('site_id', '=', $siteId)->where('slug', '=', $slug)
            ->whereHas(
                'components',
                static fn(Builder $query) => $query
                    ->select(['page_id'])
                    ->where('component', '=', $componentClassName)
                    ->limit(1)
            )
            ->firstOrNew();
    }

    public function save(Page $page): bool
    {
        return $page->save();
    }

    public function delete(Page $page): bool
    {
        return $page->delete();
    }
}
