<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\PremisesCard\Layouts;

use Closure;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class RoutesLayout extends Rows
{
    public function __construct(private readonly Fieldable|Groupable|Closure $tabFooter)
    {
//        $this->title(trans('kelnik-estate::admin.components.premisesCard.route.title'));
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
                ->title('kelnik-estate::admin.components.premisesCard.route.prefix')
                ->mask(['regex' => '[a-z0-9\-_]+'])
                ->maxlength(50)
                ->style('width:150px')
                ->value($route ? $routeProvider?->getPrefixFromPath($route->path) ?? '' : ''),
            Switcher::make('routes.ignore_page_slug')
                ->title('kelnik-estate::admin.ignorePageSlug')
                ->sendTrueOrFalse()
                ->value($route?->ignore_page_slug ?? false),
            Input::make('routes.id')->type('hidden')->value($route?->getKey() ?? 0),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
