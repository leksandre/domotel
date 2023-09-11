<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\About\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Page\View\Components\About\DataProvider;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

final class ContentLayout extends Rows
{
    public function __construct(private Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        return [
            Input::make('data.content.title')
                ->title('kelnik-page::admin.components.about.titleField')
                ->placeholder('kelnik-page::admin.components.about.titlePlaceholder')
                ->maxlength(255),
            Quill::make('data.content.text')
                ->title('kelnik-page::admin.components.about.text'),

            Input::make('data.content.button.text')
                ->maxlength(DataProvider::BUTTON_TEXT_LIMIT)
                ->title('kelnik-page::admin.components.about.buttonText'),
            Input::make('data.content.button.link')
                ->maxlength(DataProvider::BUTTON_LINK_LIMIT)
                ->title('kelnik-page::admin.components.about.buttonLink')
                ->help('kelnik-page::admin.components.about.buttonHelp'),

            Matrix::make('data.content.factoids')
                ->title('kelnik-page::admin.components.about.factoidHeader')
                ->sortable(true)
                ->columns([
                    trans('kelnik-page::admin.components.about.factoidTitle') => 'title',
                    trans('kelnik-page::admin.components.about.factoidText') => 'text'
                ])
                ->fields([
                     'title' => Input::make(),
                     'text' => TextArea::make()->style('height: 60px !important')
                ]),

            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
