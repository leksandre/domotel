<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Layouts\Category;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\News\Models\Category;
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
            TD::make('id', trans('kelnik-news::admin.id'))
                ->sort()
                ->filter(TD::FILTER_NUMERIC)
                ->defaultHidden(),
            TD::make('title', trans('kelnik-news::admin.title'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(fn(Category $category) => Link::make($category->title)->route(
                    $coreService->getFullRouteName('news.elements'),
                    ['category[]' => $category->id]
                )),
//            TD::make('slug', trans('kelnik-news::admin.slug'))
//                ->filter(TD::FILTER_TEXT)
//                ->sort(),
            TD::make('elements_count', trans('kelnik-news::admin.elementsCount'))
                ->render(static fn(Category $category) => $category->elements_count),
            TD::make()
                ->render(static function (Category $category) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';
                    $str .= '<div class="form-group mb-0">' .
                                \view('kelnik-core::platform.booleanState', ['state' => $category->active]) .
                            '</div>';
                    $str .= Link::make()->icon('bs.pencil')
                            ->route($coreService->getFullRouteName('news.category'), $category);
                    $str .= Button::make()->icon('bs.trash3')
                                ->action(route(
                                    $coreService->getFullRouteName('news.category'),
                                    [$category, 'method' => 'removeCategory']
                                ))
                                ->confirm(trans('kelnik-news::admin.deleteConfirm', ['title' => $category->title]));
                    $str .= '</div>';

                    return $str;
                })->cantHide(),
            TD::make('created_at', trans('kelnik-news::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-news::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
        ];
    }
}
