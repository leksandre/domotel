<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\PremisesPlanType;

use Kelnik\Core\Platform\Fields\Slug;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Kelnik\Estate\Repositories\Contracts\ComplexRepository;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

final class EditLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Select::make('type.complex_id')
                ->title('kelnik-estate::admin.premisesPlanType.complex')
                ->options(resolve(ComplexRepository::class)->getAll()->pluck('title', 'id')->toArray())
                ->required(),
            Input::make('type.title')
                ->title('kelnik-estate::admin.title')
                ->id('field-type-title')
                ->required(),
            Slug::make('type.slug')
                ->title('kelnik-estate::admin.slug')
                ->maxlength(255)
                ->source('field-type-title')
                ->method('transliterate'),

            Picture::make('type.list_image_id')
                ->title(trans('kelnik-estate::admin.premisesPlanType.listImage'))
                ->targetId()
                ->storage(config('kelnik-estate.storage.disk'))
                ->groups(EstateServiceProvider::MODULE_NAME)
                ->hr(),
            Picture::make('type.card_image_id')
                ->title(trans('kelnik-estate::admin.premisesPlanType.cardImage'))
                ->targetId()
                ->storage(config('kelnik-estate.storage.disk'))
                ->groups(EstateServiceProvider::MODULE_NAME)
                ->hr(),
            Input::make('type.external_id')
                ->title('kelnik-estate::admin.external_id')
                ->maxlength(255),

            Button::make(trans('kelnik-estate::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveRow')
        ];
    }
}
