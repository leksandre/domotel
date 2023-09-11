<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Building;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Platform\Layouts\BaseListLayout;
use Orchid\Icons\IconComponent;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;

final class ListLayout extends BaseListLayout
{
    protected string $routeToEdit = 'estate.building.edit';
    protected string $routeToList = 'estate.building.list';

    protected function columns(): array
    {
        /** @var CoreService $coreService */
        $coreService = $this->query->get('coreService');

        return [
            TD::make('title', trans('kelnik-estate::admin.title'))
                ->render(
                    fn(Building $building) => resolve(IconComponent::class, [
                            'path' => 'kelnik.sort',
                            'width' => '1.5em',
                            'height' => '1.5em',
                            'class' => 'handle me-3 float-start'
                        ])->render()() .
                        ' ' .
                        Link::make('[' . $building->getKey() . '] ' . $building->title)
                            ->route($coreService->getFullRouteName($this->routeToEdit), $building)
                ),
            TD::make('complex', trans('kelnik-estate::admin.building.complex'))
                ->render(static function (Building $building) use ($coreService) {
                    if (!$building->complex->exists) {
                        return '-';
                    }

                    return Link::make('[' . $building->complex->getKey() . '] ' . $building->complex->title)
                        ->target('_blank')
                        ->route($coreService->getFullRouteName('estate.complex.edit'), [$building->complex]);
                }),
            TD::make('floors', trans('kelnik-estate::admin.building.floors'))
                ->render(
                    static fn(Building $building) => Link::make((string)$building->floors_count)
                        ->route(
                            $coreService->getFullRouteName('estate.floor.list'),
                            ['building[]' => $building->getKey()]
                        )
                ),
            TD::make('sections', trans('kelnik-estate::admin.building.sections'))
                ->render(
                    static fn(Building $building) => Link::make((string)$building->sections_count)
                        ->route(
                            $coreService->getFullRouteName('estate.section.list'),
                            ['building[]' => $building->getKey()]
                        )
                ),
            TD::make('created_at', trans('kelnik-estate::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-estate::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make()
                ->render(fn(Building $building) => $this->getControls($building))
                ->cantHide()
        ];
    }
}
