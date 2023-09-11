<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Screens;

use Kelnik\Form\Platform\Layouts\Form\ListLayout;
use Kelnik\Form\Repositories\Contracts\FormRepository;
use Orchid\Screen\Actions\Link;

final class FormListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-form::admin.menu.forms');

        return [
            'list' => resolve(FormRepository::class)->getAdminListPaginated(),
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-form::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName('form.edit'))
        ];
    }

    public function layout(): array
    {
        return [
            ListLayout::class
        ];
    }
}
