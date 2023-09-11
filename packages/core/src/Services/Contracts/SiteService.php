<?php

declare(strict_types=1);

namespace Kelnik\Core\Services\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Core\Models\Site;

interface SiteService
{
    public const ACTIVE_SITES_CACHE_ID = 'sites_active';
    public const PRIMARY_SITE_CACHE_ID = 'sites_primary';
    public const SITES_CACHE_TAG = 'sites_tag';
    public const ROUTE_PARAM_NAME = 'kelnik_site_id';
    public const ROUTE_HOST_NAME = 'kelnik_host';

    public function findByPrimaryKey(int|string $siteId): ?Site;

    public function findByHost(string $host): ?Site;

    public function findPrimary(): ?Site;

    public function getAll(): Collection;

    public function getActive(): Collection;

    public function getActivePrimary(): ?Site;

    public function makeUrl(Site $site, ?string $path): string;

    public function current(): ?Site;

    public function getSeoRobotsRoutes(): void;
}
