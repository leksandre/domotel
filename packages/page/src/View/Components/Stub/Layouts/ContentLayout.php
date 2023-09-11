<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Stub\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Page\Providers\PageServiceProvider;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
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
                ->title('kelnik-page::admin.componentData.title')
                ->placeholder('kelnik-page::admin.componentData.titlePlaceholder')
                ->maxlength(255)
                ->required(),
            Quill::make('data.content.text')
                ->title('kelnik-page::admin.componentData.text')
                ->placeholder('kelnik-page::admin.componentData.textPlaceholder')
                ->help('kelnik-page::admin.componentData.textHelp')
                ->hr(),
            Input::make('data.content.phone')
                ->title('kelnik-page::admin.componentData.phone')
                ->placeholder('+7 999 999-99-99')
                ->mask('+7 999 999-99-99')
                ->required(),
            Input::make('data.content.email')
                ->title('kelnik-page::admin.componentData.email')
                ->placeholder('example@example.ru')
                ->mask('email')
                ->required()
            ->hr(),
            Picture::make('data.content.logo')
                ->title('kelnik-page::admin.componentData.logo')
                ->targetId()
                ->groups(PageServiceProvider::MODULE_NAME)
                ->required(),
            Picture::make('data.content.background')
                ->title('kelnik-page::admin.componentData.background')
                ->targetId()
                ->groups(PageServiceProvider::MODULE_NAME)
                ->required()
                ->hr(),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
