<?php

declare(strict_types=1);

namespace Kelnik\Progress\Platform\Screens;

use Kelnik\Progress\Platform\Layouts\Album\ListLayout;
use Orchid\Screen\Actions\Link;

final class AlbumListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-progress::admin.menu.albums');

        return [
            'coreService' => $this->coreService,
            'list' => $this->albumRepository->getAdminList()
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-progress::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName('progress.album'))
        ];
    }

    public function layout(): array
    {
        return [
            ListLayout::class
        ];
    }
}
