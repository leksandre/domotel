<?php

declare(strict_types=1);

namespace Kelnik\Form\Fields;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

abstract class PlatformDataLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Input::make('field.title')->title('kelnik-form::admin.fieldTitle')->maxlength(255)->required(),
            Input::make('field.params.attributes.placeholder')->title('kelnik-form::admin.placeholder')->maxlength(255),
//            Input::make('field.params.description')->title('kelnik-form::admin.description')->maxlength(255),
            Switcher::make('field.active')->title('kelnik-form::admin.active')->sendTrueOrFalse(),
            Switcher::make('field.params.attributes.required')->title('kelnik-form::admin.required')->sendTrueOrFalse(),
            Button::make(trans('kelnik-form::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveField')
        ];
    }
}
