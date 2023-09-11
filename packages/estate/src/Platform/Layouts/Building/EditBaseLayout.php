<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Building;

use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Kelnik\Estate\Repositories\Contracts\CompletionRepository;
use Kelnik\FBlock\Platform\Services\BlockPlatformService;
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
        $completionRepo = resolve(CompletionRepository::class);

        return [
            Relation::make('building.complex_id')
                ->title('kelnik-estate::admin.building.complex')
                ->fromModel(Complex::class, 'title')
                ->applyScope('adminList')
                ->required(),
            Input::make('building.title')
                ->title('kelnik-estate::admin.title')
                ->required(),
            Switcher::make('building.active')->title('kelnik-estate::admin.active')->sendTrueOrFalse(),
            Select::make('building.completion_id')
                ->title('kelnik-estate::admin.building.completion')
                ->options($completionRepo->getAll()->pluck('title', 'id')->toArray())
                ->empty(trans('kelnik-estate::admin.noValue'), BlockPlatformService::NO_VALUE)
                ->hr(),

            Input::make('building.floor_min')
                ->type('number')
                ->title('kelnik-estate::admin.floor_min'),
            Input::make('building.floor_max')
                ->type('number')
                ->title('kelnik-estate::admin.floor_max')
                ->hr(),

            Picture::make('building.complex_plan_image_id')
                ->title(trans('kelnik-estate::admin.building.complex_plan'))
                ->targetId()
                ->storage(config('kelnik-estate.storage.disk'))
                ->groups(EstateServiceProvider::MODULE_NAME)
                ->hr(),

            Input::make('building.external_id')
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
