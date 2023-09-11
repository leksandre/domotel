<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Layouts\Element;

use Illuminate\Support\Facades\Route;
use Kelnik\News\Models\Element;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

final class ListLayout extends Table
{
    protected $target = 'list';

    protected function columns(): array
    {
        $isCategory = str_contains(Route::current()->getName(), 'category.');
        $routeName = $isCategory ? 'news.category.element' : 'news.element';
        $coreService = $this->query->get('coreService');

        return [
            TD::make('id', trans('kelnik-news::admin.id'))
                ->sort()
                ->filter(TD::FILTER_NUMERIC)
                ->defaultHidden(),
            TD::make('title', trans('kelnik-news::admin.title'))
                ->sort()
                ->render(static function (Element $el) use ($routeName, $isCategory, $coreService) {
                    $routeParams = ['element' => $el];

                    if ($isCategory) {
                        $routeParams['category'] = $el->category_id;
                    }

                    return Link::make($el->title)->route($coreService->getFullRouteName($routeName), $routeParams);
                }),
            TD::make('slug', trans('kelnik-news::admin.slug'))
                ->defaultHidden()
                ->sort(),
            TD::make('category_id', trans('kelnik-news::admin.category'))
                ->sort()
                ->render(static fn(Element $el) => $el->category->title ?? '-'),
            TD::make('publish_date', trans('kelnik-news::admin.publishDate'))
                ->sort()
                ->dateTimeString(),
            TD::make('active_date_start', trans('kelnik-news::admin.dateStart'))
                ->sort()
                ->defaultHidden()
                ->dateTimeString(),
            TD::make('active_date_finish', trans('kelnik-news::admin.dateFinish'))
                ->sort()
                ->defaultHidden()
                ->dateTimeString(),
            TD::make()
                ->render(static function (Element $el) use ($routeName, $isCategory, $coreService) {
                    $routeParams = ['element' => $el];
                    if ($isCategory) {
                        $routeParams['category'] = $el->category_id;
                    }
                    $str = '<div class="admin-page-list_menu">';
                    $str .= '<div class="form-group mb-0">' .
                                \view('kelnik-core::platform.booleanState', ['state' => $el->active]) .
                            '</div>';
                    $str .= Link::make()->icon('bs.pencil')
                            ->route($coreService->getFullRouteName($routeName), $routeParams);
                    $routeParams['method'] = 'removeElement';
                    $str .= Button::make()->icon('bs.trash3')
                                ->action(route($coreService->getFullRouteName($routeName), $routeParams))
                                ->confirm(trans('kelnik-news::admin.deleteConfirm', ['title' => $el->title]));
                    $str .= '</div>';

                    return $str;
                })
                ->cantHide(false),
            TD::make('created_at', trans('kelnik-news::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-news::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
        ];
    }
}
