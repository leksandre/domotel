<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Premises;

use Kelnik\Estate\Models\Planoplan;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class EditPlanoplanLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Input::make('premises.planoplan_code')
                ->title('kelnik-estate::admin.premises.planoplanCode')
                ->maxlength(Planoplan::CODE_MAX_LENGTH),

            Button::make(trans('kelnik-estate::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveRow')
        ];
    }
}
