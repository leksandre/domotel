<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Screens;

use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;

final class CategoryElementEditScreen extends ElementEditScreen
{
    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-news::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route(
                    $this->coreService->getFullRouteName('news.category.elements'),
                    ['category' => $this->categoryId]
                ),

            Button::make(trans('kelnik-news::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeElement')
                ->confirm(trans('kelnik-news::admin.deleteConfirm', ['title' => $this->name]))
                ->canSee($this->exists),
        ];
    }
}
