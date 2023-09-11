<?php

declare(strict_types=1);

namespace Kelnik\Progress\Platform\Screens;

use Kelnik\Progress\Platform\Layouts\Group\ListLayout;
use Orchid\Screen\Actions\Link;

final class GroupListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-progress::admin.menu.groups');

        return [
            'list' => $this->groupRepository->getAdminList(),
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-progress::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName('progress.group'))
        ];
    }

    public function layout(): array
    {
        return [
            ListLayout::class
        ];
    }
}
