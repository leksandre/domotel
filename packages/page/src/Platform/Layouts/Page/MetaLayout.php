<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Layouts\Page;

use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Page\Models\Contracts\PageMeta;
use Kelnik\Page\Providers\PageServiceProvider;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class MetaLayout extends Rows
{
    protected function fields(): array
    {
        /** @var PageMeta $meta */
        $meta = $this->query->get('page')?->meta;

        return [
            Input::make('page.meta.title')
                ->title('kelnik-page::admin.meta.title')
                ->maxlength(255)
                ->value($meta->getTitle()),

            Input::make('page.meta.description')
                ->title('kelnik-page::admin.meta.description')
                ->maxlength(255)
                ->value($meta->getDescription()),

            Input::make('page.meta.keywords')
                ->title('kelnik-page::admin.meta.keywords')
                ->maxlength(255)
                ->value($meta->getKeywords())
                ->help('kelnik-page::admin.meta.keywordsHelp'),

            Picture::make('page.meta.image_id')
                ->title('kelnik-page::admin.meta.image')
                ->groups(PageServiceProvider::MODULE_NAME)
                ->value($meta->getImage()?->getKey())
                ->targetId(),

            Button::make(trans('kelnik-page::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('savePage')
        ];
    }
}
