<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Orchid\Icons\IconComponent;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\TD;

abstract class BaseListLayout extends BaseListPaginatedLayout
{
    protected $target = 'list';
    protected $template = 'kelnik-core::platform.layouts.tableSortable';

    protected function columns(): array
    {
        /** @var CoreService $coreService */
        $coreService = $this->query->get('coreService');

        return [
            TD::make('title', trans('kelnik-estate::admin.title'))
                ->render(
                    fn(EstateModel $model) => resolve(IconComponent::class, [
                            'path' => 'kelnik.sort',
                            'width' => '1.5em',
                            'height' => '1.5em',
                            'class' => 'handle me-3 float-start'
                        ])->render()() .
                        ' ' .
                        Link::make('[' . $model->getKey() . '] ' . $model->title)
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
                ->cantHide()
        ];
    }
}
