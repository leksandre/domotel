<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Screens;

use Kelnik\Form\Models\Log;
use Kelnik\Form\Platform\Layouts\Log\FullListLayout;
use Orchid\Screen\Actions\Link;

final class LogFullListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-form::admin.menu.logs');

        return [
            'coreService' => $this->coreService,
            'list' => Log::filters()
                ->defaultSort('created_at', 'desc')
                ->with('form')
                ->paginate(),
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('kelnik-form::admin.back')
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('form.list'))
        ];
    }

    public function layout(): array
    {
        return [
            FullListLayout::class
        ];
    }
}
