<?php

declare(strict_types=1);

namespace Kelnik\Page\Services\Contracts;

interface PageCache
{
    public const PAGE_CACHE = 'page';
    public const PAGE_ACTIVE_COMPONENTS_CACHE = 'pageActiveComponents';

    public function getPageCacheTag(int|string $key): ?string;

    public function getPageCacheKey(int|string $key, int|string $siteId): ?string;

    public function getPageActiveComponentsCacheKey(int $pageId);

    public function getPageComponentCacheTag(int|string $key): ?string;

    public function getDynComponentCacheTag(string $routeName): ?string;
}
