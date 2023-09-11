<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Complex;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Platform\Layouts\BaseListLayout;
use Orchid\Icons\IconComponent;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\TD;

final class ListLayout extends BaseListLayout
{
    protected string $routeToEdit = 'estate.complex.edit';
    protected string $routeToList = 'estate.complex.list';

    protected function columns(): array
    {
        /** @var CoreService $coreService */
        $coreService = $this->query->get('coreService');

        return [
            TD::make('title', trans('kelnik-estate::admin.title'))
                ->render(
                    fn(Complex $complex) => resolve(IconComponent::class, [
                            'path' => 'kelnik.sort',
                            'width' => '1.5em',
                            'height' => '1.5em',
                            'class' => 'handle me-3 float-start'
                        ])->render()() .
                        ' ' .
                        Link::make('[' . $complex->getKey() . '] ' . $complex->title)
                            ->route($coreService->getFullRouteName($this->routeToEdit), $complex)
                )
                ->filter(Input::make()),
            TD::make('buildings', trans('kelnik-estate::admin.complex.buildings'))
                ->render(
                    static fn(Complex $complex) => Link::make((string)$complex->buildings_count)
                        ->route(
                            $coreService->getFullRouteName('estate.building.list'),
                            ['complex[]' => $complex->getKey()]
                        )
                ),
            TD::make('created_at', trans('kelnik-estate::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-estate::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make()
                ->render(fn(Complex $model) => $this->getControls($model))
                ->cantHide()
        ];
    }
}
