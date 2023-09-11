<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Screens;

use Kelnik\News\Platform\Layouts\Element\ListLayout;
use Kelnik\News\Platform\Layouts\NewsFilterSelection;
use Kelnik\News\Repositories\Contracts\ElementRepository;
use Orchid\Screen\Actions\Link;

class ElementListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-news::admin.menu.elements');

        return [
            'coreService' => $this->coreService,
            'list' => resolve(ElementRepository::class)->getAdminListPaginatedBySelection(NewsFilterSelection::class)
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-news::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName('news.element'))
        ];
    }

    public function layout(): array
    {
        return [
            NewsFilterSelection::class,
            ListLayout::class
        ];
    }
}
