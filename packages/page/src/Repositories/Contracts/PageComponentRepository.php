<?php

declare(strict_types=1);

namespace Kelnik\Page\Repositories\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Models\PageComponentRoute;

interface PageComponentRepository
{
    /**
     * @param string|string[] $className
     *
     * @return Collection
     */
    public function getActiveComponentsByClassName(string|array $className): Collection;

    public function getComponentsByPage(int|string $siteId, int|string $pageId): Collection;

    public function getPageDynamicComponentFirstRoute(int $pageId): ?PageComponentRoute;

    public function findByPrimary(int|string $primary): PageComponent;

    public function save(PageComponent $pageComponent): bool;

    public function delete(PageComponent $pageComponent): bool;
}
