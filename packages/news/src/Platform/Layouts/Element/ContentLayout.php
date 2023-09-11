<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Layouts\Element;

use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Core\Platform\Fields\Upload;
use Kelnik\News\Models\Contracts\ElementButton;
use Kelnik\News\Providers\NewsServiceProvider;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class ContentLayout extends Rows
{
    protected function fields(): array
    {
        /** @var ?ElementButton $button */
        $button = $this->query->get('element')?->button;

        return [
            Picture::make('element.body_image')
                ->title('kelnik-news::admin.bodyImage')
                ->targetId()
                ->groups(NewsServiceProvider::MODULE_NAME),
            Upload::make('element.images')
                ->title('kelnik-news::admin.images')
                ->acceptedFiles('image/*')
                ->maxFiles(20)
                ->resizeWidth(1920)
                ->resizeHeight(1080)
                ->help('kelnik-news::admin.imagesHelp')
                ->groups(NewsServiceProvider::MODULE_NAME),
            Quill::make('element.body')
                ->title('kelnik-news::admin.body'),

            Title::make('')->value(trans('kelnik-news::admin.button')),

            Input::make('element.button.text')
                ->title('kelnik-news::admin.buttonText')
                ->value($button->getText()),
            Input::make('element.button.link')
                ->type('url')
                ->title('kelnik-news::admin.buttonLink')
                ->value($button->getLink()),
            Switcher::make('element.button.target')
                ->title('kelnik-news::admin.buttonTarget')
                ->sendTrueOrFalse()
                ->value($button->getTarget())
                ->addBeforeRender(fn() => $this->set('value', $this->get('value') === ElementButton::EXTERNAL_TARGET)),

            Button::make(trans('kelnik-news::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveElement')
        ];
    }
}
