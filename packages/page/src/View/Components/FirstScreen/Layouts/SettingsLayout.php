<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\FirstScreen\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\ColorPicker;
use Kelnik\Page\Platform\Fields\TemplateSelector;
use Kelnik\Page\View\Components\FirstScreen\DataProvider;
use Kelnik\Page\View\Components\FirstScreen\FirstScreen;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\RadioButtons;
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
                ->title('kelnik-page::admin.components.firstScreen.alias')
                ->mask(['regex' => '[a-z0-9\-_]+'])
                ->maxlength(150),
            Switcher::make('active')
                ->title('kelnik-page::admin.active')
                ->sendTrueOrFalse(),
            RadioButtons::make('data.content.fullHeight')
                ->title('kelnik-page::admin.components.firstScreen.fullHeight.title')
                ->options([
                    'disabled' => trans('kelnik-page::admin.components.firstScreen.fullHeight.disabled'),
                    'all' => trans('kelnik-page::admin.components.firstScreen.fullHeight.forAll'),
                    'desktop' => trans('kelnik-page::admin.components.firstScreen.fullHeight.forLaptop')
                ])
                ->help(trans('kelnik-page::admin.components.firstScreen.fullHeight.help')),
            Switcher::make('data.content.animated')
                ->title('kelnik-page::admin.components.firstScreen.animated')
                ->sendTrueOrFalse(),
            Switcher::make('data.content.fullWidth')
                ->title('kelnik-page::admin.components.firstScreen.fullWidth.title')
                ->sendTrueOrFalse()
                ->help(trans('kelnik-page::admin.components.firstScreen.fullWidth.help')),
            ColorPicker::make('data.content.bgColor')
                ->title('kelnik-page::admin.components.firstScreen.bgColor.title')
                ->help('kelnik-page::admin.components.firstScreen.bgColor.help')
                ->set('data-default', DataProvider::DEFAULT_BG_COLOR)
                ->hr(),
            TemplateSelector::make('data.content.template')
                ->title('kelnik-page::admin.components.firstScreen.design')
                ->options(FirstScreen::getTemplates())
                ->required()
                ->addBeforeRender(function () {
                    if ($this->get('value') !== null) {
                        return;
                    }

                    $this->set('value', FirstScreen::getTemplates()->first()?->name);
                }),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
