<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Screens\Selector;

use Kelnik\EstateVisual\Platform\Layouts\Selector\ListLayout;
use Kelnik\EstateVisual\Platform\Screens\BaseScreen;
use Kelnik\EstateVisual\Repositories\Contracts\SelectorRepository;
use Orchid\Screen\Actions\Link;

final class ListScreen extends BaseScreen
{
    public function query(): array
    {
        $this->name = trans('kelnik-estate-visual::admin.menu.selector');

        return [
            'list' => resolve(SelectorRepository::class)->getAllForAdminPaginated(),
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-estate-visual::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName('estateVisual.selector.edit'))
        ];
    }

    public function layout(): array
    {
        return [
            ListLayout::class
        ];
    }
}
