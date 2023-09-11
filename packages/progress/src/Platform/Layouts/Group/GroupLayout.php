<?php

declare(strict_types=1);

namespace Kelnik\Progress\Platform\Layouts\Group;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class GroupLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Input::make('group.title')
                ->title('kelnik-progress::admin.albumTitle')
                ->maxlength(255)
                ->required(),
            Switcher::make('group.active')->title('kelnik-progress::admin.active')->sendTrueOrFalse(),

            Button::make(trans('kelnik-progress::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveGroup')
        ];
    }
}
