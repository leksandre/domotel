<?php

declare(strict_types=1);

namespace Kelnik\FBlock\View\Components\BlockList\Layouts;

use Closure;
use Kelnik\FBlock\View\Components\BlockList\BlockList;
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
                ->title('kelnik-fblock::admin.components.blockList.alias')
                ->mask(['regex' => '[a-z0-9\-_]+'])
                ->maxlength(150),
            Switcher::make('active')
                ->title('kelnik-fblock::admin.active')
                ->sendTrueOrFalse(),
            Select::make('data.template')
                ->title('kelnik-fblock::admin.components.blockList.template')
                ->options(BlockList::getTemplates()->pluck('title', 'name')->toArray())
                ->required(),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
