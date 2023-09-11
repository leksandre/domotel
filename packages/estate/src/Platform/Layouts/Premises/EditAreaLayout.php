<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Premises;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class EditAreaLayout extends Rows
{
    protected function fields(): array
    {
        $area = Input::make('premises.area_total')
            ->title('kelnik-estate::admin.premises.areaTotal')
            ->type('number')
            ->min(0)
            ->step(0.01);

        return [
            $area,
            (clone $area)
                ->set('name', 'premises.area_living')
                ->title('kelnik-estate::admin.premises.areaLiving'),
            (clone $area)
                ->set('name', 'premises.area_kitchen')
                ->title('kelnik-estate::admin.premises.areaKitchen'),

            Button::make(trans('kelnik-estate::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveRow')
        ];
    }
}
