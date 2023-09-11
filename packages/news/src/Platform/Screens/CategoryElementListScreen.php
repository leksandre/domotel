<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Screens;

use Illuminate\Support\Facades\Route;
use Kelnik\News\Platform\Layouts\Element\ListLayout;
use Kelnik\News\Platform\Layouts\NewsCategoryFilterSelection;
use Kelnik\News\Repositories\Contracts\CategoryRepository;
use Kelnik\News\Repositories\Contracts\ElementRepository;
use Orchid\Screen\Actions\Link;

final class CategoryElementListScreen extends ElementListScreen
{
    private int $categoryId = 0;

    public function query(): array
    {
        $this->name = trans('kelnik-news::admin.menu.elements');
        $this->categoryId = (int)Route::current()->parameter('category');
        $category = resolve(CategoryRepository::class)->findByPrimary($this->categoryId);

        if ($category->exists) {
            $this->name = $category->title;
        }

        return [
            'coreService' => $this->coreService,
            'list' => resolve(ElementRepository::class)->getAdminListPaginatedBySelectionAndCategory(
                $category,
                NewsCategoryFilterSelection::class
            )
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-news::admin.add'))
                ->icon('bs.plus-circle')
                ->route(
                    $this->coreService->getFullRouteName('news.category.element'),
                    ['category' => $this->categoryId]
                )
        ];
    }

    public function layout(): array
    {
        return [
            NewsCategoryFilterSelection::class,
            ListLayout::class
        ];
    }
}
