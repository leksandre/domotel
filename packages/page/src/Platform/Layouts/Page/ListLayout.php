<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Layouts\Page;

use Illuminate\Routing\Route;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

final class ListLayout extends Table
{
    protected $target = 'list';
    protected $template = 'kelnik-page::platform.layouts.pageTable';

    /** @return TD[] */
    protected function columns(): array
    {
        /**
         * @var CoreService $coreService
         * @var PageLinkService $pageLinkService
         * @var SiteService $siteService
         */
        $coreService = $this->query->get('coreService');
        $pageLinkService = $this->query->get('pageLinkService');
        $siteService = $this->query->get('siteService');
        $site = $this->query->get('site');

        return [
            TD::make('id', trans('kelnik-page::admin.id'))
                ->sort()
                ->filter(TD::FILTER_NUMERIC)
                ->defaultHidden(),
            TD::make('title', trans('kelnik-page::admin.title'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(static fn(Page $page) => Link::make($page->title)
                    ->style('font-weight: bold')
                    ->icon('bs.file-earmark')
                    ->class('btn btn-link admin-page-list_truncate')
                    ->route(
                        $coreService->getFullRouteName('page.components'),
                        ['site' => $page->site_id, 'page' => $page->id]
                    )),
            TD::make('slug', trans('kelnik-page::admin.url'))
                ->render(function (Page $page) use ($pageLinkService, $siteService, $site) {
                    $componentRoute = $page->components->first(
                        static fn(PageComponent $component) => $component->active && $component->isDynamic()
                    )?->routes->first();

                    if ($componentRoute) {
                        /** @var ?Route $route */
                        $route = app()->router->getRoutes()->getByName(
                            $pageLinkService->getPageComponentRouteName($componentRoute)
                        );

                        if ($route) {
                            return '<span class="opacity-50 p-2">/' . $route->uri . '</span>';
                        }
                    }

                    $href = $page->getUrl();

                    return Link::make($href)->href($siteService->makeUrl($site, $href))->target('_blank');
                })
                ->filter(TD::FILTER_TEXT)
                ->sort(),
            TD::make()
                ->render(static function (Page $page) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';
                    if ($page->type->isSimple()) {
                        $str .= '<div class="form-group mb-0">' .
                            \view('kelnik-core::platform.booleanState', ['state' => $page->active]) .
                            '</div>';
                    }
                    $str .= Link::make()->icon('bs.gear')
                            ->route(
                                $coreService->getFullRouteName('page.edit'),
                                ['site' => $page->site_id, 'page' => $page->id]
                            );
                    $str .= Link::make()->icon('bs.grid')
                            ->route(
                                $coreService->getFullRouteName('page.components'),
                                ['site' => $page->site_id, 'page' => $page->id]
                            );
                    $str .= Button::make()->icon('bs.trash3')
                                ->action(route(
                                    $coreService->getFullRouteName('page.edit'),
                                    [
                                        'site' => $page->site_id,
                                        'page' => $page->id,
                                        'method' => 'removePage'
                                    ]
                                ))
                                ->canSee($page->type->isSimple())
                                ->confirm(trans('kelnik-page::admin.deleteConfirm', ['title' => $page->title]));
                    $str .= '</div>';

                    return $str;
                }),
            TD::make('created_at', trans('kelnik-page::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-page::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
        ];
    }
}
