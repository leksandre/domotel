<?php

declare(strict_types=1);

namespace Kelnik\Document\Platform\Layouts\Category;

use Kelnik\Document\Models\Category;
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
        $coreService = $this->query->get('coreService');

        return [
//            TD::make('id', trans('kelnik-document::admin.id'))
//                ->sort()
//                ->filter(TD::FILTER_NUMERIC)
//                ->defaultHidden(),
            TD::make('title', trans('kelnik-document::admin.title'))
                ->render(function (Category $category) use ($coreService) {
                    return resolve(IconComponent::class, [
                            'path' => 'kelnik.sort',
                            'width' => '1.5em',
                            'height' => '1.5em',
                            'class' => 'handle me-3 float-start'
                        ])->render()() .
                        ' ' .
                        Link::make($category->title)
                            ->route($coreService->getFullRouteName('document.category'), $category);
                }),
            TD::make('slug', trans('kelnik-document::admin.slug')),
            TD::make('group.title', trans('kelnik-document::admin.group')),
            TD::make('elements_count', trans('kelnik-document::admin.elementsCount'))
                ->render(static fn(Category $category) => $category->elements_count),
            TD::make('created_at', trans('kelnik-document::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-document::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make()
                ->render(static function (Category $category) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';
                    $str .= '<div class="form-group mb-0">' .
                                \view('kelnik-core::platform.booleanState', ['state' => $category->active]) .
                            '</div>';
                    $str .= Link::make()->icon('pencil')
                            ->route($coreService->getFullRouteName('document.category'), $category);
                    $str .= Button::make()->icon('bs.trash3')
                        ->action(route(
                            $coreService->getFullRouteName('document.category'),
                            ['category' => $category, 'method' => 'removeCategory']
                        ))
                        ->confirm(trans('kelnik-document::admin.deleteConfirm', ['title' => $category->title]));
                    $str .= '</div>';

                    return $str;
                })->cantHide()
        ];
    }
}
