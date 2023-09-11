<?php

declare(strict_types=1);

namespace Kelnik\Contact\Platform\Layouts\Office;

use Kelnik\Contact\Providers\ContactServiceProvider;
use Kelnik\Core\Map\Contracts\Coords;
use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Picture;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Map;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class EditLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Input::make('office.title')
                ->title('kelnik-contact::admin.title')
                ->maxlength(255)
                ->required(),
            Switcher::make('office.active')->title('kelnik-contact::admin.active')->sendTrueOrFalse(),
            Input::make('office.region')->title('kelnik-contact::admin.region')->maxlength(255),
            Input::make('office.city')->title('kelnik-contact::admin.city')->maxlength(255),
            Input::make('office.street')->title('kelnik-contact::admin.street')->maxlength(255),
            Input::make('office.phone')
                ->title('kelnik-contact::admin.phone')
                ->maxlength(255)
                ->mask(['regex' => '[0-9()\-+ ]+']),
            Input::make('office.email')->type('email')->title('kelnik-contact::admin.email')->maxlength(255),
            Input::make('office.route_link')->title('kelnik-contact::admin.route_link')->maxlength(255),
            Picture::make('office.image_id')
                ->title(trans('kelnik-contact::admin.image'))
                ->targetId()
                ->groups(ContactServiceProvider::MODULE_NAME),
            Map::make('office.coords')
                ->title('kelnik-contact::admin.coords')
                ->addBeforeRender(function () {
                    $val = $this->get('value');

                    if ($val instanceof Coords) {
                        $this->set(
                            'value',
                            [
                                'lat' => $val->lat ?: null,
                                'lng' => $val->lng ?: null
                            ]
                        );
                    }
                }),
            Matrix::make('office.schedule')
                ->title('kelnik-contact::admin.schedule.title')
                ->sortable(true)
                ->columns([
                    trans('kelnik-contact::admin.schedule.day') => 'day',
                    trans('kelnik-contact::admin.schedule.time') => 'time',
                ])
                ->fields([
                    'day' => Input::make()->maxlength(255),
                    'time' => Input::make()->maxlength(255)
                ]),
            Button::make(trans('kelnik-contact::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveOffice')
        ];
    }
}
