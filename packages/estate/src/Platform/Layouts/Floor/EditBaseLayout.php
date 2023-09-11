<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Floor;

use Kelnik\Estate\Models\Building;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class EditBaseLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Relation::make('floor.building_id')
                ->title('kelnik-estate::admin.floor.building')
                ->fromModel(Building::class, 'title')
                ->applyScope('adminList')
                ->displayAppend('admin_title')
                ->required(),
            Input::make('floor.title')
                ->title('kelnik-estate::admin.title')
                ->required(),
            Input::make('floor.number')
                ->title('kelnik-estate::admin.floor.number')
                ->value(0)
                ->required(),
            Switcher::make('floor.active')->title('kelnik-estate::admin.active')->sendTrueOrFalse(),

            Input::make('floor.external_id')
                ->title('kelnik-estate::admin.external_id')
                ->maxlength(255)
                ->hr(),

            Button::make(trans('kelnik-estate::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveRow')
        ];
    }
}
