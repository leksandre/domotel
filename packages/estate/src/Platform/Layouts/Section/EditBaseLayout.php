<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Section;

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
            Relation::make('section.building_id')
                ->title('kelnik-estate::admin.section.building')
                ->fromModel(Building::class, 'title')
                ->applyScope('adminList')
                ->displayAppend('admin_title')
                ->required(),
            Input::make('section.title')
                ->title('kelnik-estate::admin.title')
                ->required(),
            Switcher::make('section.active')->title('kelnik-estate::admin.active')->sendTrueOrFalse(),

            Input::make('section.floor_min')
                ->type('number')
                ->title('kelnik-estate::admin.floor_min'),
            Input::make('section.floor_max')
                ->type('number')
                ->title('kelnik-estate::admin.floor_max')
                ->hr(),

            Input::make('section.external_id')
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
