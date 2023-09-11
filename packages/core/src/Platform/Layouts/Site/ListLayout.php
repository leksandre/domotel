<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Layouts\Site;

use Kelnik\Core\Models\Site;
use Orchid\Icons\IconComponent;
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
            TD::make('id', trans('kelnik-core::admin.site.id'))
                ->sort()
                ->filter(TD::FILTER_NUMERIC)
                ->defaultHidden(),
            TD::make('title', trans('kelnik-core::admin.site.title'))
                ->render(
                    static fn(Site $el) => Link::make($el->title)
                        ->route($coreService->getFullRouteName('site.edit'), $el)
                ),
            TD::make('hosts', trans('kelnik-core::admin.site.hosts'))
                ->render(
                    static fn(Site $el) => $el->hosts->isEmpty()
                        ? '-'
                        : $el->hosts->pluck('value')->implode('<br>')
                ),
            TD::make()
                ->render(static function (Site $site) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';

                    if ($site->primary) {
                        $str .= '<div class="form-group mb-0">';
                        $str .= resolve(
                            IconComponent::class,
                            ['path' => 'star', 'class' => 'kelnik-site_primary']
                        )->render()();
                        $str .= '</div>';
                    }

                    $str .= '<div class="form-group mb-0">' .
                        \view('kelnik-core::platform.booleanState', ['state' => $site->active]) .
                        '</div>';
                    $str .= Link::make()->icon('bs.pencil')
                        ->route($coreService->getFullRouteName('site.edit'), $site);

                    $routeParams['site'] = $site;
                    $routeParams['method'] = 'removeSite';
                    $str .= Button::make()->icon('bs.trash3')
                        ->action(route($coreService->getFullRouteName('site.edit'), $routeParams))
                        ->confirm(trans('kelnik-core::admin.site.deleteConfirm', ['title' => $site->title]));
                    $str .= '</div>';

                    return $str;
                })
                ->cantHide(false),
            TD::make('created_at', trans('kelnik-core::admin.site.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-core::admin.site.updated'))
                ->dateTimeString()
                ->defaultHidden(),
        ];
    }
}
