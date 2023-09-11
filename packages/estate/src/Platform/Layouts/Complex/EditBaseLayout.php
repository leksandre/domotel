<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Complex;

use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Core\Platform\Fields\Slug;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class EditBaseLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Input::make('complex.title')
                ->title('kelnik-estate::admin.title')
                ->id('field-complex-title')
                ->required(),
//            Slug::make('row.slug')
//                ->title('kelnik-contact::admin.slug')
//                ->maxlength(255)
//                ->source('field-complex-title')
//                ->help('kelnik-estate::admin.slugHelp')
//                ->method('transliterate'),
            Switcher::make('complex.active')
                ->title('kelnik-estate::admin.active')
                ->sendTrueOrFalse()
                ->hr(),

//            Picture::make('complex.cover_image_id')
//                ->title(trans('kelnik-estate::admin.complex.cover'))
//                ->targetId()
//                ->groups(EstateServiceProvider::MODULE_NAME),
//            Picture::make('complex.logo_image_id')
//                ->title(trans('kelnik-estate::admin.complex.logo'))
//                ->targetId()
//                ->groups(EstateServiceProvider::MODULE_NAME)
//                ->hr(),

//            Input::make('complex.site_url')
//                ->title('kelnik-estate::admin.complex.site_url')
//                ->maxlength(255),
            Input::make('complex.floor_min')
                ->type('number')
                ->title('kelnik-estate::admin.floor_min'),
            Input::make('complex.floor_max')
                ->type('number')
                ->title('kelnik-estate::admin.floor_max')
                ->hr(),
            Input::make('complex.external_id')
                ->title('kelnik-estate::admin.external_id')
                ->maxlength(255)
                ->hr(),

//            Matrix::make('complex.options')
//                ->title('kelnik-estate::admin.complex.options')
//                ->columns([trans('kelnik-estate::admin.title') => 'text'])
//                ->fields(['text' => Input::make()])
//                ->sortable(true)
//                ->hr(),

//            Quill::make('complex.description')->title('kelnik-estate::admin.complex.description'),

            Button::make(trans('kelnik-estate::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveRow')
        ];
    }
}
