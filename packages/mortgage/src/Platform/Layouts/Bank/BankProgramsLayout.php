<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Platform\Layouts\Bank;

use Kelnik\Core\Platform\Fields\Matrix;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class BankProgramsLayout extends Rows
{
    protected function fields(): array
    {
        $setDefaultValue = function ($field) {
            $val = $field->get('value');

            if (is_string($val) && !$val) {
                $field->set('value', 0);
            }
        };

        return [
            Matrix::make('programs')
                ->title('kelnik-mortgage::admin.programs')
                ->sortable(true)
                ->columns([
                    'ID' => 'id',
                    trans('kelnik-mortgage::admin.active') => 'active',
                    trans('kelnik-mortgage::admin.title') => 'title',
                    trans('kelnik-mortgage::admin.description') => 'description',
                    trans('kelnik-mortgage::admin.minTime') => 'min_time',
                    trans('kelnik-mortgage::admin.maxTime') => 'max_time',
                    trans('kelnik-mortgage::admin.minPaymentPercent') => 'min_payment_percent',
                    trans('kelnik-mortgage::admin.maxPaymentPercent') => 'max_payment_percent',
                    trans('kelnik-mortgage::admin.rate') => 'rate',
                ])
                ->fields([
                    'id' => Input::make()
                        ->readonly()
                        ->style('width:30px; background:none; margin:0; padding: .2rem'),
                    'active' => Switcher::make()->sendTrueOrFalse(),
                    'title' => Input::make(),
                    'description' => Input::make(),
                    'min_time' => Input::make()
                        ->type('number')
                        ->min(0)
                        ->addBeforeRender(fn() => $setDefaultValue($this))
                        ->style('width: 80px;'),
                    'max_time' => Input::make()
                        ->type('number')
                        ->min(0)
                        ->addBeforeRender(fn() => $setDefaultValue($this))
                        ->style('width: 80px;'),
                    'min_payment_percent' => Input::make()
                        ->type('number')
                        ->step(0.5)
                        ->min(0)
                        ->addBeforeRender(fn() => $setDefaultValue($this))
                        ->style('width: 80px;'),
                    'max_payment_percent' => Input::make()
                        ->type('number')
                        ->step(0.5)
                        ->min(0)
                        ->addBeforeRender(fn() => $setDefaultValue($this))
                        ->style('width: 80px;'),
                    'rate' => Input::make()
                        ->type('number')
                        ->min(0)
                        ->step(0.1)
                        ->addBeforeRender(fn() => $setDefaultValue($this))
                        ->style('width: 80px;'),
                ]),

            Button::make(trans('kelnik-mortgage::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveBank')
        ];
    }
}
