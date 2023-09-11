<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Premises;

use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Models\PremisesPlanType;
use Kelnik\Estate\Models\Section;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class EditBaseLayout extends Rows
{
    protected function fields(): array
    {
        $typeRelation = Select::make('premises.type_id')
            ->title('kelnik-estate::admin.premises.type')
            ->options($this->query->get('types'))
            ->required();

        $statusRelation = Select::make('premises.status_id')
            ->title('kelnik-estate::admin.premises.status')
            ->options($this->query->get('statuses'))
            ->required();

        $planTypeTableName = (new PremisesPlanType())->getTable();

        return [
            Relation::make('premises.floor_id')
                ->title('kelnik-estate::admin.premises.floor')
                ->fromModel(Floor::class, 'title')
                ->applyScope('adminList')
                ->displayAppend('admin_title')
                ->required(),
            Relation::make('premises.section_id')
                ->title('kelnik-estate::admin.premises.section')
                ->fromModel(Section::class, 'title')
                ->applyScope('adminList')
                ->displayAppend('admin_title')
                ->allowEmpty(),
            Switcher::make('premises.active')
                ->title('kelnik-estate::admin.active')
                ->sendTrueOrFalse(),
            Switcher::make('premises.action')
                ->title('kelnik-estate::admin.action')
                ->sendTrueOrFalse()
                ->hr(),
            Input::make('premises.title')
                ->title('kelnik-estate::admin.title')
                ->required(),
            Input::make('premises.number')
                ->title('kelnik-estate::admin.premises.number')
                ->maxlength(Premises::NUMBER_MAX_LENGTH),
            Input::make('premises.number_on_floor')
                ->title('kelnik-estate::admin.premises.number_on_floor')
                ->maxlength(Premises::NUMBER_MAX_LENGTH),
            Input::make('premises.rooms')
                ->title('kelnik-estate::admin.premises.rooms')
                ->type('number')
                ->min(0)
                ->hr(),

            $typeRelation,
            (clone $typeRelation)
                ->set('name', 'premises.original_type_id')
                ->title('kelnik-estate::admin.premises.originalType')
                ->required(false)
                ->hr(),

            $statusRelation,
            (clone $statusRelation)
                ->set('name', 'premises.original_status_id')
                ->title('kelnik-estate::admin.premises.originalStatus')
                ->required(false)
                ->hr(),

            Relation::make('premises.plan_type_id')
                ->title('kelnik-estate::admin.premises.planType')
                ->fromModel(PremisesPlanType::class, $planTypeTableName . '.title')
                ->applyScope('adminList')
                ->displayAppend('admin_title'),

            Input::make('premises.external_id')
                ->title('kelnik-estate::admin.external_id')
                ->maxlength(Premises::EXTERNAL_ID_MAX_LENGTH),

            Input::make('premises.vr_link')
                ->title('kelnik-estate::admin.premises.vrLink'),

            Button::make(trans('kelnik-estate::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveRow')
        ];
    }
}
