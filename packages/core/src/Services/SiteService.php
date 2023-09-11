<?php

declare(strict_types=1);

namespace Kelnik\Core\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Kelnik\Core\Http\Controllers\SeoRobotsController;
use Kelnik\Core\Models\Host;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Repositories\Contracts\SiteRepository;

final class SiteService implements Contracts\SiteService
{
    public function __construct(private readonly SiteRepository $siteRepository)
    {
    }

    public function findByPrimaryKey(int|string $siteId): Site
    {
        return Cache::tags([self::SITES_CACHE_TAG])
            ->remember(
                'site_' . $siteId,
                null,
                fn() => $this->siteRepository->findByPrimaryKey($siteId)
            );
    }

    public function findByHost(string $host): ?Site
    {
        if (mb_strlen($host)) {
            $host = idn_to_utf8($host);

            /** @var Site $site */
            foreach ($this->getAll() as $site) {
                if ($site->hosts->isEmpty()) {
                    continue;
                }
                foreach ($site->hosts as $siteHost) {
                    if ($siteHost->value === $host) {
                        return $site;
                    }
                }
            }
        }

        return $this->findPrimary();
    }

    public function findPrimary(): ?Site
    {
        $site = Cache::tags([self::SITES_CACHE_TAG])
            ->rememberForever(self::PRIMARY_SITE_CACHE_ID, fn() => $this->siteRepository->findPrimary());

        return $site->exists ? $site : null;
    }

    public function getAll(): Collection
    {
        return $this->siteRepository->getAll();
    }

    public function getActive(): Collection
    {
        return Cache::tags([self::SITES_CACHE_TAG])
            ->rememberForever(self::ACTIVE_SITES_CACHE_ID, fn() => $this->siteRepository->getActive());
    }

    public function getActivePrimary(): ?Site
    {
        return $this->getActive()->first(static fn(Site $site) => $site->primary);
    }

    public function current(): ?Site
    {
        $route ??= Route::current();
        $siteId = (int)Arr::get($route?->defaults, self::ROUTE_PARAM_NAME, 0);

        if ($siteId && $site = $this->findByPrimaryKey($siteId)) {
            return $site;
        }

        $host = $route?->domain() ?? Request::host();

        if ($host && $site = $this->findByHost($host)) {
            return $site;
        }

        return $this->findPrimary();
    }

    public function makeUrl(Site $site, ?string $path): string
    {
        $isConsole = App::runningInConsole();
        $curHost = $isConsole
            ? null
            : Route::current()->getDomain();

        $scheme = $isConsole
            ? parse_url(config('app.url'), PHP_URL_SCHEME)
            : '//';

        $host = $site->primary
            ? ($isConsole
                ? parse_url(config('app.url'), PHP_URL_HOST)
                : null
            )
            : $site->hosts->first(static fn(Host $host) => $curHost === null || $curHost === $host->value)?->value;

        return $host
            ? $scheme . $host . $path
            : $path;
    }

    public function getSeoRobotsRoutes(): void
    {
        $checkRepository = app()->runningUnitTests() || app()->runningInConsole();
        $sites = $checkRepository && $this->siteRepository->hasSite()
            ? $this->getActive()
            : new Collection([new Site()]);

        /** @var Site $site */
        foreach ($sites as $site) {
            if ($site->hosts->isEmpty()) {
                Route::middleware(['web'])
                    ->get('robots.txt', SeoRobotsController::class)
                    ->name('kelnik.site.' . $site->getKey() . '.robots.txt')
                    ->setDefaults([$this::ROUTE_PARAM_NAME => $site->getKey()]);

                continue;
            }

            foreach ($site->hosts as $host) {
                Route::domain($host->value)
                    ->middleware(['web'])
                    ->get('robots.txt', SeoRobotsController::class)
                    ->name('kelnik.site.' . $site->getKey() . '-' . $host->getKey() . '.robots.txt')
                    ->setDefaults([$this::ROUTE_PARAM_NAME => $site->getKey()]);
            }
        }
    }
}
