<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Screens;

use Kelnik\Form\Models\Form;
use Kelnik\Form\Models\Log;
use Kelnik\Form\Platform\Layouts\Log\ListLayout;
use Orchid\Screen\Actions\Link;

final class LogListScreen extends Screen
{
    private ?Form $form = null;

    public function query(Form $form): array
    {
        $this->form = $form;
        $this->name = trans('kelnik-form::admin.menu.logs') . ': ' . $form->title;

        return [
            'coreService' => $this->coreService,
            'form' => $this->form,
            'list' => Log::filters()
                ->where('form_id', $form->getKey())
                ->defaultSort('created_at', 'desc')
                ->with('form')->paginate(),
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
            ListLayout::class
        ];
    }
}
