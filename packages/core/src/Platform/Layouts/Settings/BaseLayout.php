<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Layouts\Settings;

use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Services\Contracts\SettingsService;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class BaseLayout extends Rows
{
    protected function fields(): array
    {
        /** @var SettingsService $settingsService */
        $settingsService = resolve(SettingsService::class);

        return [
            Input::make('complex.name')
                ->title('kelnik-core::admin.settings.base.complexName')
                ->maxlength(150)
                ->required(),
            Input::make('complex.phone')
                ->title('kelnik-core::admin.settings.base.phone')
                ->mask(['regex' => '[0-9()\-+ ]+'])
                ->maxlength(50)
                ->required(),
            Input::make('complex.email')
                ->type('email')
                ->title('kelnik-core::admin.settings.base.email')
                ->required(),
            Input::make('complex.emailReply')
                ->type('email')
                ->title('kelnik-core::admin.settings.base.emailReply')
                ->required(),

            Title::make('')->value(trans('kelnik-core::admin.settings.base.animation.title')),
            Switcher::make('complex.animation.active')
                ->title('kelnik-core::admin.settings.base.animation.active')
                ->sendTrueOrFalse(),

            Title::make('')->value(trans('kelnik-core::admin.settings.base.rounding.title')),
            Switcher::make('complex.rounding.active')
                ->title('kelnik-core::admin.settings.base.rounding.active')
                ->sendTrueOrFalse(),

            Title::make('')->value(trans('kelnik-core::admin.settings.base.logo.title')),
            Picture::make('complex.logoLight')
                ->title('kelnik-core::admin.settings.base.logo.light')
                ->targetId()
                ->groups(CoreServiceProvider::MODULE_NAME),
            Picture::make('complex.logoDark')
                ->title('kelnik-core::admin.settings.base.logo.dark')
                ->targetId()
                ->groups(CoreServiceProvider::MODULE_NAME)
                ->help('kelnik-core::admin.settings.base.logo.help'),
            Picture::make('complex.favicon')
                ->title('kelnik-core::admin.settings.base.favicon.title')
                ->help(trans(
                    'kelnik-core::admin.settings.base.favicon.help',
                    [
                        'sizes' => $settingsService::FAVICON_MAX_WIDTH . 'x' . $settingsService::FAVICON_MAX_HEIGHT
                    ]
                ))
                ->groups(CoreServiceProvider::MODULE_NAME)
                ->targetId()
                ->hr(),

            Button::make(trans('kelnik-core::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveSettings')
        ];
    }
}
