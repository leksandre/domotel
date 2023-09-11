<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

abstract class BaseListPaginatedLayout extends Table
{
    protected $target = 'list';
    protected string $routeToList = '';
    protected string $routeToEdit = '';

    /** @return TD[] */
    protected function columns(): array
    {
        /** @var CoreService $coreService */
        $coreService = $this->query->get('coreService');

        return [
            TD::make('title', trans('kelnik-estate::admin.title'))
                ->render(
                    fn(EstateModel $model) => Link::make('[' . $model->getKey() . '] ' . $model->title)
                        ->route($coreService->getFullRouteName($this->routeToEdit), $model)
                )
                ->filter(Input::make()),
            TD::make('created_at', trans('kelnik-estate::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-estate::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make()
                ->render(fn(EstateModel $model) => $this->getControls($model))
                ->cantHide(false)
        ];
    }

    protected function getControls(EstateModel $model): string
    {
        $str = '<div class="admin-page-list_menu">';
        $coreService = $this->query->get('coreService');

        if ($model->getAttribute('active') !== null) {
            $str .= '<div class="form-group mb-0">' .
                \view('kelnik-core::platform.booleanState', ['state' => $model->active]) .
                '</div>';
        }
        $str .= Link::make()->icon('pencil')->route($coreService->getFullRouteName($this->routeToEdit), $model);
        $str .= Button::make()->icon('bs.trash3')
            ->action(route($coreService->getFullRouteName($this->routeToEdit), [$model, 'method' => 'removeRow']))
            ->confirm(trans('kelnik-estate::admin.deleteConfirm', ['title' => $model->title]));
        $str .= '</div>';

        return $str;
    }
}
