<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\News\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Kelnik\News\Models\Category;
use Kelnik\News\Models\Element;
use Kelnik\News\Services\Contracts\NewsService;
use Kelnik\News\View\Components\Element\ElementDto;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Tests\Feature\PageComponentTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;

final class ElementTest extends TestCase
{
    use RefreshDatabase;
    use PageComponentTrait;
    use NewsTrait;
    use SiteTrait;

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

    private function createNewsElement(): Element
    {
        /** @var Category $category */
        $category = Category::factory()->createOne(['active' => true]);

        return Element::factory()->createOne(['category_id' => $category->getKey(), 'active' => true]);
    }

    public function testActiveNewsElementShowedOnPage()
    {
        $element = $this->createNewsElement();

        $componentDto = new ElementDto();
        $componentDto->primary = $element->getKey();

        $component = new \Kelnik\News\View\Components\Element\Element($componentDto);
        $html = $component->render()?->render() ?? '';

        $this->assertStringContainsString($element->title, $html);
        $this->assertStringContainsString(trans('kelnik-news::front.element.permanentAction'), $html);
        $this->assertStringNotContainsString($element->slug, $html);
    }

    public function testInactiveNewsElementNotShowedOnPage()
    {
        $element = $this->createNewsElement();
        $element->active = false;
        $element->save();

        $componentDto = new ElementDto();
        $componentDto->primary = $element->getKey();

        $component = new \Kelnik\News\View\Components\Element\Element($componentDto);
        $html = $component->render()?->render() ?? '';

        $this->assertTrue($html === '');
    }

    public function testInactiveNewsCategoryNotShowedOnPage()
    {
        $element = $this->createNewsElement();
        $category = $element->category;
        $category->active = false;
        $category->save();

        $componentDto = new ElementDto();
        $componentDto->primary = $element->getKey();

        $component = new \Kelnik\News\View\Components\Element\Element($componentDto);
        $html = $component->render()?->render() ?? '';

        $this->assertTrue($html === '');
    }

    public function testEmptyResultOnEmptyPrimary()
    {
        $componentDto = new ElementDto();

        $component = new \Kelnik\News\View\Components\Element\Element($componentDto);
        $html = $component->render()?->render() ?? '';

        $this->assertTrue($html === '');
    }

    public function testActiveNewsElementShowedOnPageUsingCache()
    {
        $element = $this->createNewsElement();

        $componentDto = new ElementDto();
        $componentDto->primary = $element->getKey();

        $cacheId = $this->newsService->getElementCacheTag($element->getKey());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($element);
        Cache::swap($partialCacheMock);

        $component = new \Kelnik\News\View\Components\Element\Element($componentDto);
        $html = $component->render()?->render() ?? '';

        $this->assertStringContainsString(htmlspecialchars($element->title, ENT_QUOTES), $html);
        $this->assertStringContainsString(trans('kelnik-news::front.element.permanentAction'), $html);
        $this->assertStringNotContainsString($element->slug, $html);
    }

    public function testActiveNewsElementShowedOnPageWithCardLink()
    {
        $faker = Factory::create(config('app.faker_locale'));
        $element = $this->createNewsElement();

        // Create card page
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPageForCategory($element->category);

        $componentDto = new ElementDto();
        $componentDto->primary = $element->getKey();
        $componentDto->templateData['buttonText'] = $faker->text;
        $componentDto->templateData['buttonLink'] = $faker->url;

        $component = new \Kelnik\News\View\Components\Element\Element($componentDto);
        $route = $this->pageLinkService->getRouteNameByElement(
            $this->site,
            Category::class,
            $element->category_id
        );

        $this->app['router']->getRoutes()->refreshNameLookups();

        $html = $component->render()?->render() ?? '';
        unset($page, $pageDynComponent, $pageDynComponentClassname, $faker, $componentDto, $route);

        $cacheId = $this->newsService->getElementCacheTag($element->id);

        $this->assertTrue(Route::getRoutes()->hasNamedRoute($cardRouteName));
        $this->assertTrue(Cache::tags([
            $this->newsService->getCategoryCacheTag($element->category_id),
            $cacheId,
            $this->pageService->getDynComponentCacheTag($cardRouteName)
        ])->has($cacheId));
        $this->assertDatabaseCount(resolve(PageComponentRoute::class)->getTable(), 1);
        $this->assertStringContainsString(htmlspecialchars($element->title, ENT_QUOTES), $html);
        $this->assertStringContainsString($element->slug, $html);
    }
}
