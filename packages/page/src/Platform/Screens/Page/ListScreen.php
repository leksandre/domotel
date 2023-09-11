<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Screens\Page;

use Illuminate\Support\Facades\Route;
use Kelnik\Page\Platform\Layouts\Page\ListLayout;
use Kelnik\Page\Platform\Screens\Screen;
use Kelnik\Page\Repositories\Contracts\PageRepository;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;

final class ListScreen extends Screen
{
    private int $siteId = 0;

    public function query(): array
    {
        $this->siteId = (int)Route::current()->parameter('site');
        $this->name = trans('kelnik-page::admin.menuTitle');

        return [
            'coreService' => $this->coreService,
            'pageService' => resolve(PageService::class),
            'pageLinkService' => resolve(PageLinkService::class),
            'siteService' => $this->siteService,
            'sites' => $this->siteService->getAll(),
            'site' => $this->siteService->findByPrimaryKey($this->siteId),
            'list' => resolve(PageRepository::class)->getAdminListBySite($this->siteId),
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-page::admin.addPage'))
                ->icon('bs.plus-circle')
                ->route(
                    $this->coreService->getFullRouteName('page.edit'),
                    ['site' => $this->siteId]
                )
        ];
    }

    /** @return Layout[]|string[] */
    public function layout(): array
    {
        return [
            ListLayout::class
        ];
    }
}
