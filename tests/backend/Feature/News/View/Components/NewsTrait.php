<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\News\View\Components;

use Illuminate\Support\Collection;
use Kelnik\News\Models\Category;
use Kelnik\News\Models\Element;
use Kelnik\News\Platform\Services\Contracts\NewsPlatformService;

trait NewsTrait
{
    private function createNewsElements(): Collection
    {
        /** @var Category $category */
        $category = Category::factory()->createOne(['active' => true]);

        return Element::factory()->count(self::ITEMS_MAX)->create([
            'category_id' => $category->getKey(),
            'active' => true,
            'active_date_finish' => now()->addMonths(5)
        ]);
    }

    private function createCardPageForCategory(Category $category): array
    {
        resolve(NewsPlatformService::class)
            ->createLinkToPage($category, [$this->site->getKey() => $this->pageLinkService::PAGE_MODULE_ROW_NEW_PAGE]);

        $route = $this->pageLinkService->getElementRoutes(
            $this->site,
            [Category::class => [$category->getKey()]]
        )->first();

        return [
            'route' => $route,
            'name' => $this->pageLinkService->getPageComponentRouteName($route)
        ];
    }
}
