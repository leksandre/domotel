<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Infrastructure\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Page\View\Components\Infrastructure\DataProvider;
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
                ->title('kelnik-page::admin.components.infrastructure.titleField')
                ->placeholder('kelnik-page::admin.components.infrastructure.titlePlaceholder')
                ->required()
                ->maxlength(255),
            Quill::make('data.content.text')
                ->title('kelnik-page::admin.components.infrastructure.text')
                ->help(trans(
                    'kelnik-page::admin.components.infrastructure.textHelp',
                    ['cnt' => DataProvider::TEXT_LIMIT]
                )),
            Matrix::make('data.content.legend')
                ->title('kelnik-page::admin.components.infrastructure.legendHeader')
                ->sortable(true)
                ->maxRows(DataProvider::UTP_LIMIT)
                ->columns([
                    trans('kelnik-page::admin.components.infrastructure.legendIcon') => 'icon',
                    trans('kelnik-page::admin.components.infrastructure.legendTitle') => 'title'
                ])
                ->fields([
                    'icon' => Picture::make()->targetId()->class('matrix_picture'),
                    'title' => Input::make()->maxlength(80)->required()
                ])
                ->help(
                    trans('kelnik-page::admin.components.infrastructure.legendHelp', ['cnt' => DataProvider::UTP_LIMIT])
                ),
            Picture::make('data.content.plan')
                ->title('kelnik-page::admin.components.infrastructure.plan')
                ->help('kelnik-page::admin.components.infrastructure.planHelp')
                ->required()
                ->targetId(),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
