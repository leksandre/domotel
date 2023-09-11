<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Screens;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Page\Platform\Services\Contracts\PagePlatformService;
use Kelnik\Page\Services\Contracts\PageLinkService;

abstract class Screen extends \Orchid\Screen\Screen
{
    protected readonly CoreService $coreService;
    protected readonly PageLinkService $pageLinkService;
    protected readonly PagePlatformService $pagePlatformService;
    protected readonly SiteService $siteService;
    protected ?string $name = null;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->pageLinkService = resolve(PageLinkService::class);
        $this->pagePlatformService = resolve(PagePlatformService::class);
        $this->siteService = resolve(SiteService::class);
    }
}
