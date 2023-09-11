<?php

declare(strict_types=1);

namespace Kelnik\News\Services\Contracts;

use Closure;
use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\News\Models\Element;
use Kelnik\News\Repositories\Contracts\CategoryRepository;
use Kelnik\News\Repositories\Contracts\ElementRepository;
use Kelnik\Page\Services\Contracts\PageLinkService;

interface NewsService
{
    public function __construct(
        CategoryRepository $categoryRepo,
        ElementRepository $elementRepo,
        CoreService $coreService,
        PageLinkService $pageLinkService
    );

    public function getAllCategories(): Collection;

    public function getActiveRowByPrimary(int|string $primary, ?array $fields = null): Element;

    public function getListByElement(
        int|array $categoryId,
        int $excludeId,
        int $count,
        Collection $cardRoutes = null
    ): Collection;

    public function prepareElements(Collection $res, Collection $cardRoutes, ?Closure $callback = null): Collection;

    public function getMinCacheTime(int $timeA, int $timeB): int;

    public function getCategoryCacheTag(int|string $id): ?string;

    public function getElementCacheTag(int|string $id): ?string;
}
