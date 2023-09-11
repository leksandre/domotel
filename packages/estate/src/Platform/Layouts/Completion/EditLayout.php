<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Completion;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class EditLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Input::make('completion.title')
                ->title('kelnik-estate::admin.title')
                ->required(),
            DateTimer::make('completion.event_date')
                ->title('kelnik-estate::admin.completion.eventDate')
                ->placeholder('kelnik-estate::admin.completion.selectDate')
                ->enableTime(false)
                ->addBeforeRender(function () {
                    if (!$this->get('value')) {
                        $this->set('value', now()->setTime(0, 0, 0));
                    }
                })
                ->required(),
            Input::make('completion.external_id')
                ->title('kelnik-estate::admin.external_id')
                ->maxlength(255),

            Button::make(trans('kelnik-estate::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveRow')
        ];
    }
}
