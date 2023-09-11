<?php

declare(strict_types=1);

namespace Kelnik\Contact\View\Components\Offices\Layouts;

use Closure;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class ContentLayout extends Rows
{
    public function __construct(private Fieldable|Groupable|Closure $tabFooter, private Fieldable $contentLink)
    {
    }

    protected function fields(): array
    {
        return [
            Input::make('data.content.title')
                ->title('kelnik-contact::admin.components.offices.titleField')
                ->placeholder('kelnik-contact::admin.components.offices.titlePlaceholder')
                ->maxlength(255),

            Input::make('data.map.zoom')->type('number')
                ->style('width:80px')
                ->title('kelnik-contact::admin.components.offices.zoom')
                ->help('kelnik-contact::admin.components.offices.zoomHelp')
                ->value(10)
                ->min(0)
                ->max(16),

            $this->contentLink,

            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
