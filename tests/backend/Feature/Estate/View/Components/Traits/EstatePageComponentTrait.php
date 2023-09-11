<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Estate\View\Components\Traits;

use Kelnik\Core\Models\Site;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Platform\Services\Contracts\PremisesTypeGroupPlatformService;
use Kelnik\Page\Services\Contracts\PageLinkService;

trait EstatePageComponentTrait
{
    private function createCardPage(PremisesTypeGroup $typeGroup): array
    {
        /**
         * @var PageLinkService $pageLinkService
         * @var Site $site
         */
        $pageLinkService = resolve(PageLinkService::class);
        $site = resolve(SiteService::class)->current();

        resolve(PremisesTypeGroupPlatformService::class)
            ->createLinkToPage($typeGroup, [$site->getKey() => $pageLinkService::PAGE_MODULE_ROW_NEW_PAGE]);

        $route = $pageLinkService->getElementRoutes(
            $site,
            [PremisesTypeGroup::class => [$typeGroup->getKey()]]
        )->first();

        return [
            'route' => $route,
            'name' => $pageLinkService->getPageComponentRouteName($route)
        ];
    }
}
