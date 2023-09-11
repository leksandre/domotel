<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Layouts\Settings;

use Kelnik\Core\Platform\Fields\Title;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\RadioButtons;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

final class MapLayout extends Rows
{
    protected function fields(): array
    {
        $query = $this->query;

        return [
            Select::make('map.service')
                ->title('kelnik-core::admin.settings.map.service')
                ->options([
                    'yandex' => trans('kelnik-core::admin.settings.map.yandex'),
//                    'google' => 'Google',
//                    'osm' => 'OpenStreetMap'
                ])
                ->required(),

            Title::make('yandex')->value(trans('kelnik-core::admin.settings.map.yandex')),

            Input::make('map.yandex.api')
                ->title('kelnik-core::admin.settings.map.api.title'),
//            Input::make('map.yandex.zoom')->type('number')
//                ->style('width:80px')
//                ->title('kelnik-core::admin.settings.map.zoom')
//                ->value(10)
//                ->min(0)
//                ->max(16),
            Input::make('map.yandex.api-search')
                ->title('kelnik-core::admin.settings.map.api.search')
                ->help('kelnik-core::admin.settings.map.api.searchHelp'),

            RadioButtons::make('map.dragMode')
                ->title('kelnik-core::admin.settings.map.dragMode.title')
                ->options($this->query->get('mapDragModeList'))
                ->addBeforeRender(function () use ($query) {
                    if ($this->get('value') === null) {
                        $this->set('value', $query->get('mapDragModeDefault')?->value);
                    }
                }),

            Button::make(trans('kelnik-core::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveSettings')
        ];
    }
}
