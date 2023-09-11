<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Layouts\Settings;

use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Core\Platform\Services\Contracts\SettingsPlatformService;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class CookieNoticeLayout extends Rows
{
    protected function fields(): array
    {
        return [
             Switcher::make('cookieNotice.active')
                 ->title('kelnik-core::admin.settings.cookieNotice.active')
                 ->sendTrueOrFalse(),
             Input::make('cookieNotice.expired')
                ->title('kelnik-core::admin.settings.cookieNotice.expired')
                ->type('number')
                ->min(SettingsPlatformService::EXPIRED_MIN)
                ->max(SettingsPlatformService::EXPIRED_MAX)
                ->addBeforeRender(function () {
                    /** @var Input $this */
                    if ($this->get('value') === null) {
                        $this->set('value', SettingsPlatformService::EXPIRED_DEFAULT);
                    }
                }),

            Input::make('cookieNotice.buttonText')
                ->title('kelnik-core::admin.settings.cookieNotice.buttonText')
                ->maxlength(SettingsPlatformService::BUTTON_MAX_LENGTH),
            Quill::make('cookieNotice.text')
                ->title('kelnik-core::admin.settings.cookieNotice.text'),
            Input::make('cookieNotice.linkText')
                ->title('kelnik-core::admin.settings.cookieNotice.linkText'),
            Input::make('cookieNotice.link')
                ->title('kelnik-core::admin.settings.cookieNotice.link')
                ->help('kelnik-core::admin.settings.cookieNotice.linkHelp'),

            Quill::make('cookieNotice.popupText')
                ->title('kelnik-core::admin.settings.cookieNotice.popupText'),

            Button::make(trans('kelnik-core::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveSettings')
        ];
    }
}
