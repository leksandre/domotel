<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\StaticList\Layouts;

use Closure;
use Kelnik\News\View\Components\StaticList\StaticList;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class SettingsLayout extends Rows
{
    public function __construct(private Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        return [
            Input::make('data.content.alias')
                ->title('kelnik-news::admin.components.staticList.alias')
                ->mask(['regex' => '[a-z0-9\-_]+'])
                ->maxlength(150),
            Switcher::make('active')
                ->title('kelnik-news::admin.active')
                ->sendTrueOrFalse(),
            Select::make('data.template')
                ->title('kelnik-news::admin.components.staticList.template')
                ->options(StaticList::getTemplates()->pluck('title', 'name')->toArray())
                ->required(),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
