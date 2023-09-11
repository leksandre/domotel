<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Screens;

use Kelnik\News\Models\Category;
use Kelnik\News\Platform\Layouts\Category\ListLayout;
use Orchid\Screen\Actions\Link;

final class CategoryListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-news::admin.menu.categories');

        return [
            'list' => Category::filters()->defaultSort('priority')->withCount('elements')->paginate(),
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-news::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName('news.category'))
        ];
    }

    public function layout(): array
    {
        return [
            ListLayout::class
        ];
    }
}
