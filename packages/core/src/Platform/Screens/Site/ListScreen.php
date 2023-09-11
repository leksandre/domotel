<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Screens\Site;

use Kelnik\Core\Platform\Layouts\Site\ListLayout;
use Kelnik\Core\Repositories\Contracts\SiteRepository;
use Orchid\Screen\Actions\Link;

final class ListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-core::admin.site.menu');

        return [
            'list' => resolve(SiteRepository::class)->getAdminList(),
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-core::admin.site.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName('site.edit'))
        ];
    }

    public function layout(): array
    {
        return [
            ListLayout::class
        ];
    }
}
