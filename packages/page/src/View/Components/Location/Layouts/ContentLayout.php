<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Location\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Picture;
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
                ->title('kelnik-page::admin.components.location.titleField')
                ->placeholder('kelnik-page::admin.components.location.titlePlaceholder')
                ->maxlength(255),
            Matrix::make('data.content.usp')
                ->title('kelnik-page::admin.components.location.usp.header')
                ->sortable(true)
                ->maxRows(50)
                ->columns([
                    trans('kelnik-page::admin.components.location.usp.icon') => 'icon',
                    trans('kelnik-page::admin.components.location.usp.title') => 'title'
                ])
                ->fields([
                     'icon' => Picture::make()
                         ->targetId()
                         ->class('matrix_picture')
                         ->groups(PageServiceProvider::MODULE_NAME),
                     'title' => Input::make()->maxlength(80)->required()
                ]),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
