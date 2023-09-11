<?php

declare(strict_types=1);

namespace Kelnik\Page\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;

interface PageRepository
{
    public function pageIsUnique(Page $page): bool;

    public function getActivePageByPrimary(int|string $primary, int|string $siteId): Page;

    public function getPageByPath(string $urlPath, int|string $siteId): Page;

    public function getErrorPage(int|string $siteId): Page;

    public function getActivePageRoutes(bool $checkTable, string $cacheId): Collection;

    public function getAdminListBySite(int|string $siteId): LengthAwarePaginator;

    /**
     * @param string[] $componentNamespace
     * @param int[]|string[] $siteId
     *
     * @return Collection
     */
    public function getPagesWithDynamicComponent(array $componentNamespace, array $siteId = []): Collection;

    public function getPagesWithoutDynamicComponents(int|string $siteId = null): Collection;

    public function addComponent(Page $page, PageComponent $pageComponent): bool;

    public function findByPrimary(int|string $primary): Page;

    public function findBySlugAndSiteWithComponent(
        string $slug,
        int|string $siteId,
        string $componentClassName
    ): Page;

    public function findPrimaryPagesBySite(int|string $siteId): Collection;

    public function save(Page $page): bool;

    public function delete(Page $page): bool;
}
