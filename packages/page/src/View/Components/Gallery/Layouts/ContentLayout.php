<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Gallery\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Upload;
use Kelnik\Page\Providers\PageServiceProvider;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
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
                ->title('kelnik-page::admin.components.gallery.titleField')
                ->placeholder('kelnik-page::admin.components.gallery.titlePlaceholder')
                ->maxlength(255),
            Upload::make('data.content.slider')
                ->title('kelnik-page::admin.components.firstScreen.slider')
                ->acceptedFiles('image/*')
                ->groups(PageServiceProvider::MODULE_NAME),

            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
