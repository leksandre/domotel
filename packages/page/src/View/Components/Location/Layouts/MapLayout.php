<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Location\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Title;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Map;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class MapLayout extends Rows
{
    public function __construct(private Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        return [
            Title::make('')->value(trans('kelnik-page::admin.components.location.header')),
            Map::make('data.map.center')->title('kelnik-page::admin.components.location.center'),
            Input::make('data.map.zoom')->type('number')
                ->style('width:80px')
                ->title('kelnik-core::admin.settings.map.zoom')
                ->help('kelnik-page::admin.components.location.zoomHelp')
                ->value(10)
                ->min(0)
                ->max(16),

            Title::make('route')->value(trans('kelnik-page::admin.components.location.route.header')),
            Switcher::make('data.map.route.active')
                ->title('kelnik-page::admin.components.location.route.active')
                ->sendTrueOrFalse(),
            Input::make('data.map.route.title')->title('kelnik-page::admin.components.location.route.title'),
            Input::make('data.map.route.link')->title('kelnik-page::admin.components.location.route.link'),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
