<?php

declare(strict_types=1);

namespace Kelnik\Menu\Platform\Screens;

use Kelnik\Menu\Platform\Layouts\Menu\ListLayout;
use Kelnik\Menu\Repositories\Contracts\MenuRepository;
use Orchid\Screen\Actions\Link;

final class ListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-menu::admin.menu.menus');

        return [
            'list' => resolve(MenuRepository::class)->getAdminList(),
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-menu::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName('menu.edit'))
        ];
    }

    public function layout(): array
    {
        return [
            ListLayout::class
        ];
    }
}
