<?php

declare(strict_types=1);

namespace Kelnik\Progress\Platform\Layouts\Album;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Progress\Models\Album;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

final class ListLayout extends Table
{
    protected $target = 'list';

    protected function columns(): array
    {
        /** @var CoreService $coreService */
        $coreService = $this->query->get('coreService');

        return [
            TD::make('id', trans('kelnik-progress::admin.id'))->defaultHidden(),
            TD::make('title', trans('kelnik-progress::admin.title'))
                ->render(
                    static fn(Album $album) => Link::make($album->title)
                            ->route($coreService->getFullRouteName('progress.album'), $album)
                ),
            TD::make('group.title', trans('kelnik-progress::admin.group')),
            TD::make('publish_date', trans('kelnik-progress::admin.publishDate'))
                ->dateTimeString('d F Y'),
            TD::make('images_count', trans('kelnik-progress::admin.imagesCount'))
                ->render(fn(Album $album) => $album->images_count),
            TD::make('videos_count', trans('kelnik-progress::admin.videosCount'))
                ->render(fn(Album $album) => $album->videos_count),
            TD::make('created_at', trans('kelnik-progress::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-progress::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),

            TD::make()
                ->render(static function (Album $album) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';
                    $str .= '<div class="form-group mb-0">' .
                        \view('kelnik-core::platform.booleanState', ['state' => $album->active]) .
                        '</div>';
                    $str .= Link::make()->icon('pencil')
                        ->route($coreService->getFullRouteName('progress.album'), $album->id);
                    $str .= Button::make()->icon('bs.trash3')
                        ->action(route(
                            $coreService->getFullRouteName('progress.album'),
                            [$album, 'method' => 'removeAlbum']
                        ))
                        ->confirm(trans('kelnik-progress::admin.deleteConfirm', ['title' => $album->title]));
                    $str .= '</div>';

                    return $str;
                })->cantHide(false),
        ];
    }
}
