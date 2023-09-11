<?php

declare(strict_types=1);

namespace Kelnik\Progress\Platform\Layouts\Camera;

use Illuminate\Support\Str;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Progress\Models\Camera;
use Orchid\Icons\IconComponent;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

final class ListLayout extends Table
{
    protected $target = 'list';
    protected $template = 'kelnik-core::platform.layouts.tableSortable';

    protected function columns(): array
    {
        /** @var CoreService $coreService */
        $coreService = $this->query->get('coreService');

        return [
//            TD::make('id', trans('kelnik-progress::admin.id'))->defaultHidden(),
            TD::make('title', trans('kelnik-progress::admin.title'))
                ->render(
                    static fn(Camera $camera) => resolve(IconComponent::class, [
                            'path' => 'kelnik.sort',
                            'width' => '1.5em',
                            'height' => '1.5em',
                            'class' => 'handle me-3 float-start'
                        ])->render()() .
                        ' ' .
                        Link::make($camera->title)
                            ->route($coreService->getFullRouteName('progress.camera'), $camera)
                ),
            TD::make('group.title', trans('kelnik-progress::admin.group')),
            TD::make('url', trans('kelnik-progress::admin.url'))
                ->render(
                    static fn(Camera $camera) => Link::make(Str::limit($camera->url, 50))
                        ->href($camera->url)
                        ->target('_blank')
                ),
            TD::make('created_at', trans('kelnik-progress::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-progress::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),

            TD::make()
                ->render(static function (Camera $camera) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';
                    $str .= '<div class="form-group mb-0">' .
                        \view('kelnik-core::platform.booleanState', ['state' => $camera->active]) .
                        '</div>';
                    $str .= Link::make()->icon('pencil')
                        ->route($coreService->getFullRouteName('progress.camera'), $camera);
                    $str .= Button::make()->icon('bs.trash3')
                        ->action(route(
                            $coreService->getFullRouteName('progress.camera'),
                            [$camera, 'method' => 'removeCamera']
                        ))
                        ->confirm(trans('kelnik-progress::admin.deleteConfirm', ['title' => $camera->title]));
                    $str .= '</div>';

                    return $str;
                })->cantHide(false),
        ];
    }
}
