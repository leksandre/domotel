<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Premises;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class EditPriceLayout extends Rows
{
    protected function fields(): array
    {
        $price = Input::make('premises.price')
            ->title('kelnik-estate::admin.premises.price')
            ->type('number')
            ->min(0)
            ->step(0.01);

        return [
            $price,
            (clone $price)
                ->set('name', 'premises.price_total')
                ->title('kelnik-estate::admin.premises.priceTotal'),
            (clone $price)
                ->set('name', 'premises.price_sale')
                ->title('kelnik-estate::admin.premises.priceSale'),
            (clone $price)
                ->set('name', 'premises.price_meter')
                ->title('kelnik-estate::admin.premises.priceMeter'),

            Button::make(trans('kelnik-estate::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveRow')
        ];
    }
}
