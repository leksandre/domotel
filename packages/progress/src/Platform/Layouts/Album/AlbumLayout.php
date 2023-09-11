<?php

declare(strict_types=1);

namespace Kelnik\Progress\Platform\Layouts\Album;

use Illuminate\Support\Carbon;
use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Core\Platform\Fields\Upload;
use Kelnik\Progress\Models\Group;
use Kelnik\Progress\Providers\ProgressServiceProvider;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class AlbumLayout extends Rows
{
    protected function fields(): array
    {
        $album = $this->query->get('album');

        return [
            Relation::make('album.group_id')
                ->title('kelnik-progress::admin.group')
                ->fromModel(Group::class, 'title')
                ->allowEmpty(),
            Input::make('album.title')
                ->title('kelnik-progress::admin.albumTitle')
                ->maxlength(255)
                ->required(),
            Switcher::make('album.active')->title('kelnik-progress::admin.active')->sendTrueOrFalse(),
            DateTimer::make('album.publish_date')
                ->title('kelnik-progress::admin.publishDate')
                ->placeholder('kelnik-progress::admin.selectDate')
                ->min(Carbon::create(1970, 1, 1))
                ->format('Y-m-d')
                ->allowInput()
                ->allowEmpty()
                ->required()
                ->addBeforeRender(function () use ($album) {
                    if (!$album->exists && !$this->get('value')) {
                        $this->set('value', now());
                    }
                }),
            Input::make('album.comment')
                ->title('kelnik-progress::admin.comment')
                ->maxlength(255),
            Quill::make('album.description')
                ->title('kelnik-progress::admin.description')
                ->help('kelnik-progress::admin.descriptionHelp')
                ->hr(),

            Title::make('')->value(trans('kelnik-progress::admin.images')),
            Upload::make('album.images')
                ->acceptedFiles('image/*')
                ->resizeWidth(1920)
                ->resizeHeight(1080)
                ->groups(ProgressServiceProvider::MODULE_NAME)
                ->hr(),

            Title::make('')->value(trans('kelnik-progress::admin.videos')),
            Matrix::make('videos')
                ->title('kelnik-progress::admin.videoList')
                ->sortable(true)
                ->help('kelnik-progress::admin.videoHelp')
                ->columns([
                    'ID' => 'id',
                    trans('kelnik-progress::admin.url') => 'url',
                ])
                ->fields([
                    'id' => Input::make()
                        ->readonly()
                        ->style('width:30px; background:none; margin:0; padding: .2rem'),
                    'url' => Input::make()->type('url')
                ]),

            Button::make(trans('kelnik-progress::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveAlbum')
        ];
    }
}
