<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Layouts\Settings;

use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Services\Contracts\SettingsService;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Code;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class JsCodeLayout extends Rows
{
    protected function fields(): array
    {
        /** @var SettingsService $settingsService */
        $settingsService = resolve(SettingsService::class);

        return [
             Matrix::make('jsCode')
                 ->sortable(true)
                 ->columns([
                     trans('kelnik-core::admin.settings.jsCodes.active') => 'active',
                     trans('kelnik-core::admin.settings.jsCodes.name') => 'title',
                     trans('kelnik-core::admin.settings.jsCodes.section') => 'section',
                     trans('kelnik-core::admin.settings.jsCodes.code') => 'code'
                 ])
                 ->fields([
                     'active' => Switcher::make()->sendTrueOrFalse(),
                     'title' => Input::make(),
                     'section' => Select::make()
                              ->options([
                                  $settingsService::JS_CODE_POSITION_HEAD => 'Head',
                                  $settingsService::JS_CODE_POSITION_BODY => 'Body'
                              ]),
                     'code' => Code::make()->language('html')
                 ])
                 ->hr(),

            Button::make(trans('kelnik-core::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveSettings')
        ];
    }
}
