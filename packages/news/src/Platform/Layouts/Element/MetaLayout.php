<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Layouts\Element;

use Kelnik\News\Models\Contracts\ElementMeta;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class MetaLayout extends Rows
{
    protected function fields(): array
    {
        /** @var ElementMeta $meta */
        $meta = $this->query->get('element')?->meta;

        return [
            Input::make('element.meta.title')
                ->title('kelnik-news::admin.meta.title')
                ->maxlength(255)
                ->value($meta->getTitle()),

            Input::make('element.meta.description')
                ->title('kelnik-news::admin.meta.description')
                ->maxlength(255)
                ->value($meta->getDescription()),

            Input::make('element.meta.keywords')
                ->title('kelnik-news::admin.meta.keywords')
                ->maxlength(255)
                ->value($meta->getKeywords())
                ->help('kelnik-news::admin.meta.keywordsHelp'),

            Button::make(trans('kelnik-news::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveElement')
        ];
    }
}
