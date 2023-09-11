<?php

declare(strict_types=1);

namespace Kelnik\Progress\Platform\Layouts\Camera;

use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Progress\Models\Group;
use Kelnik\Progress\Providers\ProgressServiceProvider;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class CameraLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Relation::make('camera.group_id')
                ->title('kelnik-progress::admin.group')
                ->fromModel(Group::class, 'title')
                ->allowEmpty(),
            Input::make('camera.title')
                ->title('kelnik-progress::admin.title')
                ->maxlength(255)
                ->required(),
            Switcher::make('camera.active')->title('kelnik-progress::admin.active')->sendTrueOrFalse(),
            Input::make('camera.url')
                ->type('url')
                ->title('kelnik-progress::admin.url')
                ->maxlength(255)
                ->required(),
//            Picture::make('camera.cover_image')
//                ->title('kelnik-progress::admin.cover')
//                ->groups(ProgressServiceProvider::MODULE_NAME)
//                ->targetId(),
//
//            Quill::make('camera.description')
//                ->title('kelnik-progress::admin.description')
//                ->help('kelnik-progress::admin.descriptionHelp')
//                ->hr(),

            Button::make(trans('kelnik-progress::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveCamera')
        ];
    }
}
