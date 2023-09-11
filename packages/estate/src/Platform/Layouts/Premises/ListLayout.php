<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Premises;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Premises;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

final class ListLayout extends Table
{
    protected string $routeToEdit = 'estate.premises.edit';
    protected string $routeToList = 'estate.premises.list';
    protected $target = 'list';

    protected function columns(): array
    {
        /** @var CoreService $coreService */
        $coreService = $this->query->get('coreService');

        return [
//            TD::make('id', trans('kelnik-estate::admin.id'))->sort(),
            TD::make('title', trans('kelnik-estate::admin.title'))
                ->render(
                    fn(Premises $premises) => Link::make('[' . $premises->getKey() . '] ' . $premises->title)
                        ->route($coreService->getFullRouteName($this->routeToEdit), $premises)
                )
                ->sort(),
            TD::make('number', trans('kelnik-estate::admin.premises.number'))
                ->sort()
                ->defaultHidden(),
            TD::make('number_on_floor', trans('kelnik-estate::admin.premises.number_on_floor'))
                ->sort()
                ->defaultHidden(),
            TD::make('floor_id', trans('kelnik-estate::admin.premises.floor'))
                ->render(
                    static fn(Premises $premises) => Link::make($premises->floor->admin_title)
                        ->target('_blank')
                        ->route($coreService->getFullRouteName('estate.floor.edit'), $premises->floor)
                )
                ->sort(),
            TD::make('section_id', trans('kelnik-estate::admin.premises.section'))
                ->render(
                    static fn(Premises $premises) => $premises->section->exists
                        ? Link::make($premises->section->admin_title)
                            ->target('_blank')
                            ->route($coreService->getFullRouteName('estate.section.edit'), $premises->section)
                        : '-'
                )
                ->sort(),
            TD::make('status_id', trans('kelnik-estate::admin.premises.status'))
                ->render(static fn(Premises $premises) => $premises->status->title)
                ->sort(),
            TD::make('type_id', trans('kelnik-estate::admin.premises.type'))
                ->render(static fn(Premises $premises) => $premises->type->admin_title)
                ->sort(),
            TD::make('area_total', trans('kelnik-estate::admin.premises.areaTotal'))
                ->render(static fn(Premises $premises) => $premises->area_total)
                ->sort()
                ->defaultHidden(),
            TD::make('price_total', trans('kelnik-estate::admin.premises.priceTotal'))
                ->render(static fn(Premises $premises) => number_format($premises->price_total, 0, ',', ' '))
                ->sort()
                ->defaultHidden(),
            TD::make('features', trans('kelnik-estate::admin.premises.features'))
                ->render(
                    static fn(Premises $premises) => $premises->features->isNotEmpty()
                        ? implode('<br>', $premises->features->pluck('title')->toArray())
                        : '-'
                )->defaultHidden(),
            TD::make('external_id', trans('kelnik-estate::admin.external_id'))->sort()->defaultHidden(),
            TD::make('created_at', trans('kelnik-estate::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-estate::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make()
                ->render(function (Premises $premises) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';

                    if ($premises->getAttribute('active') !== null) {
                        $str .= '<div class="form-group mb-0">' .
                            \view('kelnik-core::platform.booleanState', ['state' => $premises->active]) .
                            '</div>';
                    }
                    $str .= Link::make()->icon('pencil')
                        ->route($coreService->getFullRouteName($this->routeToEdit), $premises);
                    $str .= Button::make()->icon('bs.trash3')
                        ->action(route(
                            $coreService->getFullRouteName($this->routeToEdit),
                            [$premises, 'method' => 'removeRow']
                        ))
                        ->confirm(trans('kelnik-estate::admin.deleteConfirm', ['title' => $premises->title]));
                    $str .= '</div>';

                    return $str;
                })
                ->cantHide()
        ];
    }
}
