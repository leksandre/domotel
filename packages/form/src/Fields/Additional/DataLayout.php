<?php

declare(strict_types=1);

namespace Kelnik\Form\Fields\Additional;

use Kelnik\Form\Fields\PlatformDataLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;

final class DataLayout extends PlatformDataLayout
{
    protected function fields(): array
    {
        return [
            Input::make('field.title')->title('kelnik-form::admin.fieldTitle')->maxlength(255)->required(),
            Switcher::make('field.active')->title('kelnik-form::admin.active')->sendTrueOrFalse(),
            Button::make(trans('kelnik-form::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveField')
        ];
    }
}
