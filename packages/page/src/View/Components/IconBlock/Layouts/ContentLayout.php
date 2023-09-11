<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\IconBlock\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Page\View\Components\IconBlock\DataProvider;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class ContentLayout extends Rows
{
    public function __construct(private readonly Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        return [
            Input::make('data.content.title')
                ->title('kelnik-page::admin.components.iconBlock.titleField')
                ->placeholder('kelnik-page::admin.components.iconBlock.titlePlaceholder')
                ->required()
                ->maxlength(255),
            Quill::make('data.content.text')->title('kelnik-page::admin.components.iconBlock.text'),
            Input::make('data.content.lineLimit')
                ->type('number')
                ->min(DataProvider::USP_MIN)
                ->max(DataProvider::USP_MAX)
                ->title('kelnik-page::admin.components.iconBlock.lineLimit')
                ->required()
                ->addBeforeRender(function () {
                    $value = (int)$this->get('value');
                    if ($value < DataProvider::USP_MIN || $value > DataProvider::USP_MAX) {
                        $this->set('value', DataProvider::USP_MIN);
                    }
                }),
            Matrix::make('data.content.list')
                ->title('kelnik-page::admin.components.iconBlock.listHeader')
                ->sortable(true)
                ->maxRows(50)
                ->columns([
                    trans('kelnik-page::admin.components.iconBlock.listIcon') => 'icon',
                    trans('kelnik-page::admin.components.iconBlock.listTitle') => 'title',
                    trans('kelnik-page::admin.components.iconBlock.listText') => 'text'
                ])
                ->fields([
                    'icon' => Picture::make()->targetId()->class('matrix_picture'),
                    'title' => Input::make()->maxlength(DataProvider::TEXT_LIMIT)
                        ->help(trans(
                            'kelnik-page::admin.components.iconBlock.limit',
                            ['limit' => DataProvider::TEXT_LIMIT]
                        )),
                    'text' => Input::make()->maxlength(DataProvider::TEXT_LIMIT)
                        ->help(trans(
                            'kelnik-page::admin.components.iconBlock.limit',
                            ['limit' => DataProvider::TEXT_LIMIT]
                        ))
                ]),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
