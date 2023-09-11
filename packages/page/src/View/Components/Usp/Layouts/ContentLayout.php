<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Usp\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Core\Platform\Fields\Upload;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\View\Components\Usp\DataProvider;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
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
                ->title('kelnik-page::admin.components.usp.titleField')
                ->placeholder('kelnik-page::admin.components.usp.titlePlaceholder')
                ->maxlength(255),
            Upload::make('data.content.icon')
                ->title('kelnik-page::admin.components.usp.icon')
                ->help('kelnik-page::admin.components.usp.iconHelp')
                ->groups(PageServiceProvider::MODULE_NAME)
                ->maxFiles(1)
                ->acceptedFiles('image/svg+xml,.svg'),
            Quill::make('data.content.text')
                ->title('kelnik-page::admin.components.usp.text'),

            Input::make('data.content.button.text')
                ->maxlength(DataProvider::BUTTON_TEXT_LIMIT)
                ->title('kelnik-page::admin.components.usp.buttonText'),
            Input::make('data.content.button.link')
                ->maxlength(DataProvider::BUTTON_LINK_LIMIT)
                ->title('kelnik-page::admin.components.usp.buttonLink')
                ->help('kelnik-page::admin.components.usp.buttonHelp'),

            Matrix::make('data.content.options')
                ->title('kelnik-page::admin.components.usp.optionsHeader')
                ->help('kelnik-page::admin.components.usp.optionsHelp')
                ->sortable(true)
                ->maxRows(3)
                ->columns([
                    trans('kelnik-page::admin.components.usp.optionsTitle') => 'title'
                ])
                ->fields([
                     'title' => Input::make()->maxlength(80)
                ]),
            Upload::make('data.content.slider')
                ->title('kelnik-page::admin.components.usp.slider')
                ->acceptedFiles('image/*')
                ->groups(PageServiceProvider::MODULE_NAME),
            Switcher::make('data.content.multiSlider')
                ->title('kelnik-page::admin.components.usp.multiSlider')
                ->sendTrueOrFalse(),

            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
