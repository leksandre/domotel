<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\News\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Kelnik\News\Models\Element;
use Kelnik\News\Services\Contracts\NewsService;
use Kelnik\News\View\Components\OtherList\OtherList;
use Kelnik\News\View\Components\OtherList\OtherListDto;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Tests\Feature\PageComponentTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;

final class OtherListTest extends TestCase
{
    use RefreshDatabase;
    use PageComponentTrait;
    use NewsTrait;
    use SiteTrait;

    private const ITEMS_MIN = 3;
    private const ITEMS_MAX = 10;

    private NewsService $newsService;
    private PageLinkService $pageLinkService;
    private PageService $pageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->newsService = resolve(NewsService::class);
        $this->pageLinkService = resolve(PageLinkService::class);
        $this->pageService = resolve(PageService::class);
        $this->initSite();
    }

    public function testActiveNewsElementShowedOnPage()
    {
        $news = $this->createNewsElements();
        /** @var Element $currentEl */
        $currentEl = $news->shift();
        $category = $currentEl->category;
        $faker = Factory::create(config('app.faker_locale'));
        $page = $this->createPage();

        // Create card page
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPageForCategory($category);

        $componentDto = new OtherListDto();
        $componentDto->primary = $currentEl->getKey();
        $componentDto->title = $faker->sentence(4);
        $componentDto->categoryId = $category->getKey();
        $componentDto->count = self::ITEMS_MAX;
        $componentDto->pageId = $page->id;
        $componentDto->cardRoutes = new Collection([
            $category->getKey() => $cardRouteName
        ]);

        $component = new \Kelnik\News\View\Components\OtherList\OtherList($componentDto);
        $this->app['router']->getRoutes()->refreshNameLookups();

        $cacheId = $this->newsService->getElementCacheTag('page_' . $page->id . '_other_' . $componentDto->primary);
        unset($page, $pageDynComponent, $pageDynComponentClassname, $faker);

        $html = $component->render()?->render() ?? '';

        $this->assertStringContainsString($componentDto->title, $html);
        $this->assertStringNotContainsString($currentEl->title, $html);
        $this->assertStringContainsString($news->random(1)->first()->slug, $html);
        $this->assertTrue(Route::getRoutes()->hasNamedRoute($cardRouteName));
        $this->assertTrue(Cache::tags([
            $this->newsService->getCategoryCacheTag($category->getKey()),
            $this->pageService->getDynComponentCacheTag($cardRouteName)
        ])->has($cacheId));
    }

    public function testInactiveNewsElementNotShowedOnPage()
    {
        $news = $this->createNewsElements();

        /**
         * @var Element $currentEl
         * @var Element $disabledEl
         */
        $currentEl = $news->shift();

        $disabledEl = $news->random(1)->first();
        $disabledEl->active = false;
        $disabledEl->save();

        $faker = Factory::create(config('app.faker_locale'));

        $componentDto = new OtherListDto();
        $componentDto->primary = $currentEl->getKey();
        $componentDto->title = $faker->sentence(4);
        $componentDto->categoryId = $currentEl->category_id;
        $componentDto->count = self::ITEMS_MAX;

        $component = new \Kelnik\News\View\Components\OtherList\OtherList($componentDto);
        $html = $component->render()?->render() ?? '';

        $this->assertStringContainsString($componentDto->title, $html);
        $this->assertStringNotContainsString($currentEl->title, $html);
        $this->assertStringNotContainsString($disabledEl->title, $html);
    }

    public function testInactiveNewsCategoryNotShowedOnPage()
    {
        $element = $this->createNewsElements();
        $category = $element->random(1)->first()->category;
        $category->active = false;
        $category->save();

        $componentDto = new OtherListDto();
        $componentDto->primary = $element->random(1)->first()->getKey();

        $component = new OtherList($componentDto);
        $html = $component->render()?->render() ?? '';

        $this->assertStringNotContainsString($element->random(1)->first()->title, $html);
    }

    public function testActiveNewsElementShowedOnPageUsingCache()
    {
        $news = $this->createNewsElements();
        $currentEl = $news->shift();

        $componentDto = new OtherListDto();
        $componentDto->primary = $currentEl->getKey();
        $componentDto->categoryId = $currentEl->category_id;
        $componentDto->count = self::ITEMS_MAX;

        $cacheId = $this->newsService->getElementCacheTag('page_0_other_' . $currentEl->getKey());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn(['list' => $news]);
        Cache::swap($partialCacheMock);

        $component = new OtherList($componentDto);
        $html = $component->render()?->render() ?? '';

        $this->assertStringNotContainsString($currentEl->title, $html);
    }
}
