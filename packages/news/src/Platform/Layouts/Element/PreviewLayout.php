<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Layouts\Element;

use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\News\Providers\NewsServiceProvider;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Layouts\Rows;

final class PreviewLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Picture::make('element.preview_image')
                ->title('kelnik-news::admin.previewImage')
                ->targetId()
                ->groups(NewsServiceProvider::MODULE_NAME),
//            Quill::make('element.preview')->title('kelnik-news::admin.preview'),

            Button::make(trans('kelnik-news::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveElement')
        ];
    }
}
