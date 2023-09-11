<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\PremisesFeature;

use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class EditLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Input::make('feature.title')
                ->title('kelnik-estate::admin.title')
                ->required(),
            Switcher::make('feature.active')
                ->title('kelnik-estate::admin.premisesFeature.active')
                ->sendTrueOrFalse(),
            Switcher::make('feature.general')
                ->title('kelnik-estate::admin.premisesFeature.isGeneral')
                ->sendTrueOrFalse(),
            Input::make('feature.external_id')
                ->title('kelnik-estate::admin.external_id')
                ->maxlength(255)
                ->hr(),

            Matrix::make('feature.features')
                ->sortable(true)
                ->columns(
                    [
                        'ID' => 'id',
                        trans('kelnik-estate::admin.active') => 'active',
                        trans('kelnik-estate::admin.icon') => 'icon_id',
                        trans('kelnik-estate::admin.title') => 'title',
                        trans('kelnik-estate::admin.external_id') => 'external_id'
                    ]
                )
                ->title('kelnik-estate::admin.premisesFeature.subFeatures')
                ->fields([
                    'id' => Input::make()->readonly(),
                    'active' => Switcher::make()->sendTrueOrFalse(),
                    'icon_id' => Picture::make()
                        ->targetId()
                        ->class('matrix_picture')
                        ->storage(config('kelnik-estate.storage.disk'))
                        ->groups(EstateServiceProvider::MODULE_NAME),
                    'title' => Input::make()->required()->maxlength(255),
                    'external_id' => Input::make()->maxlength(255)
                ]),

            Button::make(trans('kelnik-estate::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveRow')
        ];
    }
}
