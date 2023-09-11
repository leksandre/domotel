<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Usp\Layouts;

use Closure;
use Kelnik\Core\View\Components\Contracts\HasMargin;
use Kelnik\Page\View\Components\Usp\Usp;
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
                ->title('kelnik-page::admin.components.usp.alias')
                ->mask(['regex' => '[a-z0-9\-_]+'])
                ->maxlength(150),
            Switcher::make('active')
                ->title('kelnik-page::admin.active')
                ->sendTrueOrFalse(),
            Switcher::make('data.content.textOnLeft')
                ->title('kelnik-page::admin.components.usp.textOnLeft')
                ->sendTrueOrFalse(),
            Select::make('data.margin.top')
                ->title('kelnik-core::admin.margin.top')
                ->options(Usp::getMarginVariants())
                ->value(HasMargin::MARGIN_DEFAULT)
                ->addBeforeRender(fn() => $this->set('isOptionList', false)),
            Select::make('data.margin.bottom')
                ->title('kelnik-core::admin.margin.bottom')
                ->options(Usp::getMarginVariants())
                ->value(HasMargin::MARGIN_DEFAULT)
                ->addBeforeRender(fn() => $this->set('isOptionList', false)),

            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
