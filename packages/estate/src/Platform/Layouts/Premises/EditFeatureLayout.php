<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Premises;

use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Estate\Models\PremisesFeature;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;

final class EditFeatureLayout extends Rows
{
    protected function fields(): array
    {
        $premisesFeature = new PremisesFeature();

        return [
            Relation::make('premises.features.')
                ->title('kelnik-estate::admin.premises.features')
                ->multiple()
                ->fromModel(
                    $premisesFeature::class,
                    $premisesFeature->getTable() . '.title'
                )
                ->applyScope('adminList')
                ->displayAppend('admin_title')
                ->allowEmpty(),
            Matrix::make('premises.additional_properties')
                ->title('kelnik-estate::admin.premises.additionalProps')
                ->columns([
                    trans('kelnik-estate::admin.premises.propField.key') => 'key',
                    trans('kelnik-estate::admin.premises.propField.title') => 'title',
                    trans('kelnik-estate::admin.premises.propField.value') => 'value'
                ])
                ->fields([
                    'key' => Input::make()->mask(['regex' => '[0-9_\-a-z]+'])->max(150),
                    'title' => Input::make()->max(255),
                    'value' => Input::make()->max(255)
                ])
                ->maxRows(50),
            Button::make(trans('kelnik-estate::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveRow')
        ];
    }
}
