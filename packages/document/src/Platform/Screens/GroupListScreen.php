<?php

declare(strict_types=1);

namespace Kelnik\Document\Platform\Screens;

use Kelnik\Document\Platform\Layouts\Group\ListLayout;
use Orchid\Screen\Actions\Link;

final class GroupListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-document::admin.menu.groups');

        return [
            'list' => $this->groupRepository->getAdminList(),
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-document::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName('document.group'))
        ];
    }

    public function layout(): array
    {
        return [
            ListLayout::class
        ];
    }
}
