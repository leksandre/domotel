<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Layouts\Form;

use Kelnik\Core\Platform\Fields\Matrix;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class EmailLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Input::make('form.notify_title')
                ->title('kelnik-form::admin.notifyTitle')
                ->maxlength(255),
            Matrix::make('form.emails')
                ->title('kelnik-form::admin.emailList')
                ->columns([
                    trans('kelnik-form::admin.email') => 'email'
                ])
                ->fields([
                    'email' => Input::make()->type('email')
                ]),
            Button::make(trans('kelnik-form::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveForm')
        ];
    }
}
