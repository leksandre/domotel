<?php

declare(strict_types=1);

namespace Kelnik\Contact\Platform\Layouts\Social;

use Kelnik\Contact\Providers\ContactServiceProvider;
use Kelnik\Core\Platform\Fields\Picture;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class EditLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Input::make('social.title')
                ->title('kelnik-contact::admin.title')
                ->maxlength(255)
                ->required(),
            Switcher::make('social.active')->title('kelnik-contact::admin.active')->sendTrueOrFalse(),
            Input::make('social.link')->title('kelnik-contact::admin.link')->required()->maxlength(255),
            Picture::make('social.icon_id')
                ->title(trans('kelnik-contact::admin.icon'))
                ->targetId()
                ->groups(ContactServiceProvider::MODULE_NAME),
            Button::make(trans('kelnik-contact::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveSocial')
        ];
    }
}
