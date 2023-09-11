<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Layouts\Selector;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateVisual\Models\Selector;
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
            TD::make('title', trans('kelnik-estate-visual::admin.title'))
                ->render(
                    fn(Selector $model) => Link::make($model->title)
                        ->route($coreService->getFullRouteName('estateVisual.selector.step.list'), $model)
                        ->style('font-weight: bold')
                ),
            TD::make('complex', trans('kelnik-estate-visual::admin.complex'))
                ->render(
                    fn(Selector $model) => Link::make($model->complex->title)
                        ->route($coreService->getFullRouteName('estate.complex.edit'), $model)
                ),
            TD::make('created_at', trans('kelnik-estate-visual::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-estate-visual::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make()
                ->render(function (Selector $model) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';

                    if ($model->getAttribute('active') !== null) {
                        $str .= '<div class="form-group mb-0">' .
                            \view('kelnik-core::platform.booleanState', ['state' => $model->active]) .
                            '</div>';
                    }
                    $str .= Link::make()->icon('pencil')
                        ->route($coreService->getFullRouteName('estateVisual.selector.edit'), $model);
                    $str .= Link::make()->icon('layers')
                        ->route($coreService->getFullRouteName('estateVisual.selector.step.list'), $model);
                    $str .= Button::make()->icon('bs.trash3')
                        ->action(route(
                            $coreService->getFullRouteName('estateVisual.selector.edit'),
                            [
                                $model,
                                'method' => 'removeRow'
                            ]
                        ))
                        ->confirm(trans('kelnik-estate-visual::admin.deleteConfirm', ['title' => $model->title]));
                    $str .= '</div>';

                    return $str;
                })
                ->cantHide()
        ];
    }
}
