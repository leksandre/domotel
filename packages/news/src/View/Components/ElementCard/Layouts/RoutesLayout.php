<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\ElementCard\Layouts;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class RoutesLayout extends Rows
{
    public function __construct()
    {
        $this->title(trans('kelnik-news::admin.components.elementCard.route.title'));
    }

    protected function fields(): array
    {
        $page = $this->query->get('page');
        $component = $this->query->get('component');
        $route = $component->routes?->first();
        $componentClassName = $this->query->get('componentName');
        $routeProvider = $componentClassName::initRouteProvider($page, $component);

        return [
            Input::make('routes.prefix')
                ->title('kelnik-news::admin.components.elementCard.route.prefix')
                ->mask(['regex' => '[a-z0-9\-_]+'])
                ->maxlength(50)
                ->style('width:150px')
                ->value($route ? $routeProvider?->getPrefixFromPath($route->path) ?? '' : ''),
            Switcher::make('routes.ignore_page_slug')
                ->title('kelnik-news::admin.ignorePageSlug')
                ->sendTrueOrFalse()
                ->value($route?->ignore_page_slug ?? false),
            Input::make('routes.id')->type('hidden')->value($route?->getKey() ?? 0)
        ];
    }
}
