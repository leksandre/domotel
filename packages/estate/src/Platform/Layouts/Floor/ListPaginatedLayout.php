<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Floor;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Platform\Layouts\BaseListPaginatedLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;

final class ListPaginatedLayout extends BaseListPaginatedLayout
{
    protected string $routeToEdit = 'estate.floor.edit';
    protected string $routeToList = 'estate.floor.list';

    protected function columns(): array
    {
        /** @var CoreService $coreService */
        $coreService = $this->query->get('coreService');

        return [
            TD::make('title', trans('kelnik-estate::admin.title'))
                ->render(
                    fn(Floor $floor) => Link::make('[' . $floor->getKey() . '] ' . $floor->title)
                        ->route($coreService->getFullRouteName($this->routeToEdit), $floor)
                ),
            TD::make('building', trans('kelnik-estate::admin.floor.building'))
                ->render(static function (Floor $floor) use ($coreService) {
                    $html = '[' . $floor->getComplex()?->getKey() . '] ' . $floor->getComplex()?->title .
                        ' > ' .
                        '[' . $floor->building->getKey() . '] ' . $floor->building->title;

                    return Link::make($html)
                        ->target('_blank')
                        ->route($coreService->getFullRouteName('estate.building.edit'), [$floor->building]);
                }),
            TD::make('premises', trans('kelnik-estate::admin.floor.premises'))
                ->render(
                    static fn(Floor $floor) => Link::make((string)$floor->premises_count)
                        ->route(
                            $coreService->getFullRouteName('estate.premises.list'),
                            ['floor[]' => $floor->getKey()]
                        )
                ),
            TD::make('created_at', trans('kelnik-estate::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-estate::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make()
                ->render(fn(Floor $floor) => $this->getControls($floor))
                ->cantHide()
        ];
    }
}
