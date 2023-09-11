<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Section;

use Kelnik\Estate\Models\Section;
use Kelnik\Estate\Platform\Layouts\BaseListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;

final class ListPaginatedLayout extends BaseListLayout
{
    protected string $routeToEdit = 'estate.section.edit';
    protected string $routeToList = 'estate.section.list';

    protected function columns(): array
    {
        $coreService = $this->query->get('coreService');

        return [
            TD::make('title', trans('kelnik-estate::admin.title'))
                ->render(
                    fn(Section $section) => Link::make('[' . $section->getKey() . '] ' . $section->title)
                        ->route($coreService->getFullRouteName($this->routeToEdit), $section)
                ),
            TD::make('building', trans('kelnik-estate::admin.section.building'))
                ->render(static function (Section $section) use ($coreService) {
                    $html = '[' . $section->getComplex()?->getKey() . '] ' . $section->getComplex()?->title .
                        ' > ' .
                        '[' . $section->building->getKey() . '] ' . $section->building->title;

                    return Link::make($html)
                        ->target('_blank')
                        ->route($coreService->getFullRouteName('estate.building.edit'), [$section->building]);
                }),
            TD::make('premises', trans('kelnik-estate::admin.floor.premises'))
                ->render(
                    static fn(Section $section) => Link::make((string)$section->premises_count)
                        ->route(
                            $coreService->getFullRouteName('estate.premises.list'),
                            ['section[]' => $section->getKey()]
                        )
                ),
            TD::make('created_at', trans('kelnik-estate::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-estate::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make()
                ->render(fn(Section $section) => $this->getControls($section))
                ->cantHide()
        ];
    }
}
