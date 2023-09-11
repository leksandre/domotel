<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Infrastructure\Layouts;

use Closure;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
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
                ->title('kelnik-page::admin.components.infrastructure.alias')
                ->mask(['regex' => '[a-z0-9\-_]+'])
                ->maxlength(150),
            Switcher::make('active')
                ->title('kelnik-page::admin.active')
                ->sendTrueOrFalse(),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
