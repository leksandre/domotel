<?php

declare(strict_types=1);

namespace Kelnik\Menu\Platform\Layouts\Menu;

use Kelnik\Menu\Models\Menu;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

final class ListLayout extends Table
{
    protected $target = 'list';

    protected function columns(): array
    {
        $coreService = $this->query->get('coreService');

        return [
            TD::make('id', trans('kelnik-menu::admin.id'))
                ->sort()
                ->filter(TD::FILTER_NUMERIC)
                ->defaultHidden(),
            TD::make('title', trans('kelnik-menu::admin.title'))
                ->sort()
                ->filter()
                ->render(
                    fn (Menu $menu) => Link::make($menu->title)
                        ->route($coreService->getFullRouteName('menu.edit'), $menu)
                ),
            TD::make('type', trans('kelnik-menu::admin.menuType'))
                ->render(static fn(Menu $menu) => $menu->type->title()),
            TD::make('items_count', trans('kelnik-menu::admin.items.count'))
                ->render(static fn(Menu $menu) => $menu->items_count),
            TD::make()
                ->render(static function (Menu $menu) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';
                    $str .= '<div class="form-group mb-0">' .
                                \view('kelnik-core::platform.booleanState', ['state' => $menu->active]) .
                            '</div>';
                    $str .= Link::make()->icon('bs.pencil')
                            ->route($coreService->getFullRouteName('menu.edit'), $menu);
                    $str .= Button::make()->icon('bs.trash3')
                                ->action(route(
                                    $coreService->getFullRouteName('menu.edit'),
                                    [$menu, 'method' => 'removeMenu']
                                ))
                                ->confirm(trans('kelnik-menu::admin.deleteConfirm', ['title' => $menu->title]));
                    $str .= '</div>';

                    return $str;
                })->cantHide(),
            TD::make('created_at', trans('kelnik-menu::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-menu::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
        ];
    }
}
