<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Platform\Layouts\Bank;

use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Mortgage\Providers\MortgageServiceProvider;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class BankBaseLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Input::make('bank.title')
                ->title('kelnik-mortgage::admin.title')
                ->maxlength(255)
                ->required(),
            Switcher::make('bank.active')->title('kelnik-mortgage::admin.active')->sendTrueOrFalse(),
//            Input::make('bank.priority')
//                ->type('number')
//                ->title('kelnik-mortgage::admin.priority')
//                ->maxlength(3)
//                ->value(500)
//                ->step(10),
            Input::make('bank.link')
                ->type('url')
                ->title('kelnik-mortgage::admin.link')
                ->maxlength(255),
            Picture::make('bank.logo_id')
                ->title('kelnik-mortgage::admin.logo')
                ->groups(MortgageServiceProvider::MODULE_NAME)
                ->help('kelnik-mortgage::admin.logoHelp')
                ->targetId(),
            Quill::make('bank.description')
                ->title('kelnik-mortgage::admin.description'),

            Button::make(trans('kelnik-mortgage::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveBank')
        ];
    }
}
