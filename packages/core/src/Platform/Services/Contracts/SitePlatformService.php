<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Repositories\Contracts\SiteRepository;
use Kelnik\Core\Services\Contracts\CoreService;

interface SitePlatformService
{
    public const HOSTS_MAX_COUNT = 10;

    public function __construct(SiteRepository $siteRepository, CoreService $coreService);

    public function saveSite(Site $site, Request $request): bool|RedirectResponse;
}
