<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\News\View\Components;

use Faker\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Kelnik\News\Models\Category;
use Kelnik\News\Models\Element;
use Kelnik\News\Services\Contracts\NewsService;
use Kelnik\News\View\Components\ElementCard\ElementCard;
use Kelnik\News\View\Components\OtherList\OtherListDto;
use Kelnik\Page\Repositories\Contracts\PageComponentRepository;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Tests\Feature\PageComponentTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Kelnik\Tests\TestFile;
use Mockery;

final class ElementCardTest extends TestCase
{
    use RefreshDatabase;
    use PageComponentTrait;
    use NewsTrait;
    use SiteTrait;

    private const ITEMS_MIN = 3;
    private const ITEMS_MAX = 10;

    private Filesystem $storage;
    private NewsService $newsService;
    private PageLinkService $pageLinkService;
    private PageService $pageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
        $this->newsService = resolve(NewsService::class);
        $this->pageLinkService = resolve(PageLinkService::class);
        $this->pageService = resolve(PageService::class);
        $this->initSite();
    }

    private function setElementUrl(Element $newsElement, array $routeParams, string $routeName): string
    {
        if (!$routeParams || !$routeName) {
            return '#';
        }

        $params = [];
        foreach ($routeParams as $paramName) {
            $params[$paramName] = $newsElement->{$paramName} ?? null;
        }

        return route($routeName, $params, false);
    }

    public function testActiveNewsElementAreShowed()
    {
        $news = $this->createNewsElements();
        /** @var Element $newsElement */
        $newsElement = $news->random(1)->first();

        // Create card page
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPageForCategory($newsElement->category);

        $pageDynComponent = $route->pageComponent;
        $pageDynComponent->refresh();
        $other = $pageDynComponent->data->get('other');
        $pageDynComponent->data->put('other', [
            'title' => $other['title']
        ]);
        resolve(PageComponentRepository::class)->save($pageDynComponent);
        $cardPage = $pageDynComponent->page;

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];
        $cacheId = $this->newsService->getElementCacheTag(
            'page_' . $cardPage->id . '_card_' . md5($newsElement->slug)
        );

        $newsElement->url = $this->setElementUrl($newsElement, $routeParams, $cardRouteName);

        // Meta
        $faker = Factory::create();
        $metaDescription = $faker->unique()->sentence();
        $metaKeywords = $faker->unique()->sentence();
        $newsElement->meta->setDescription($metaDescription);
        $newsElement->meta->setKeywords($metaKeywords);

        // Set preview image on first news element
        $uploaded = UploadedFile::fake();
        $bodyImage = $uploaded->image('news-body-image.jpg', 800, 600);
        $bodyImage = new TestFile($bodyImage);
        $bodyImage->setStorage($this->storage);
        $bodyImage = $bodyImage->load();
        $newsElement->body_image = $bodyImage->id;
        $newsElement->save();

        $response = $this->get($newsElement->url);
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString("<h1>{$newsElement->title}</h1>", $html);
        $this->assertStringContainsString("<title>{$newsElement->title}</title>", $html);
        $this->assertStringContainsString($newsElement->body, $html);
        $this->assertStringContainsString('<ul class="breadcrumbs">', $html);
        $this->assertStringContainsString('<div class="page-header__background">', $html);
        $this->assertStringContainsString($bodyImage->name, $html);
        $this->assertStringContainsString('<meta name="description" content="' . $metaDescription . '">', $html);
        $this->assertStringContainsString('<meta name="keywords" content="' . $metaKeywords . '">', $html);
        $this->assertTrue(Route::getRoutes()->hasNamedRoute($cardRouteName));
        $this->assertTrue(Cache::tags([
            $this->pageService->getPageComponentCacheTag($pageDynComponent->getKey()),
            $this->pageService->getDynComponentCacheTag($cardRouteName),
            $this->newsService->getCategoryCacheTag($newsElement->category_id),
            $this->newsService->getElementCacheTag($newsElement->getKey())
        ])->has($cacheId));
    }

    public function testActiveNewsElementWithWrongSlugReturnError404()
    {
        $news = $this->createNewsElements();
        $faker = Factory::create(config('app.faker_locale'));

        /** @var Element $newsElement */
        $newsElement = $news->random(1)->first();

        // Create card page
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPageForCategory($newsElement->category);

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];

        $newsElement->slug .= $faker->slug(1);
        $newsElement->url = $this->setElementUrl($newsElement, $routeParams, $cardRouteName);

        $response = $this->get($newsElement->url);

        $response->assertNotFound();
    }

    public function testInactiveNewsElementReturnError404()
    {
        $news = $this->createNewsElements();
        /** @var Element $newsElement */
        $newsElement = $news->random(1)->first();

        // Create card page
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPageForCategory($newsElement->category);

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];

        $newsElement->active = false;
        $newsElement->save();

        $newsElement->url = $this->setElementUrl($newsElement, $routeParams, $cardRouteName);

        $response = $this->get($newsElement->url);

        $response->assertNotFound();
    }

    public function testShouldReturnNotFoundErrorWithRouteOfAnotherCategory()
    {
        $news = $this->createNewsElements();
        $faker = Factory::create(config('app.faker_locale'));

        /** @var Element $newsElement */
        $newsElement = $news->random(1)->first();
        $anotherCategory = Category::factory()->createOne(['active' => true]);

        // Create card page
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPageForCategory($newsElement->category);

        // Create card page for another category
        ['route' => $anotherRoute, 'name' => $anotherCardRouteName] = $this->createCardPageForCategory(
            $anotherCategory
        );

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];

        $newsElement->url = $this->setElementUrl($newsElement, $routeParams, $cardRouteName);

        $response = $this->get($newsElement->url);
        $responseAnother = $this->get(route($anotherCardRouteName, ['slug' => $newsElement->slug], false));

        $response->assertOk();
        $responseAnother->assertNotFound();
    }

    public function testActiveNewsElementAreShowedUsingCache()
    {
        $news = $this->createNewsElements();

        /** @var Element $newsElement */
        $newsElement = $news->random(1)->first();

        // Create card page
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPageForCategory($newsElement->category);
        $pageDynComponent = $route->pageComponent;

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];

        $newsElement->url = $this->setElementUrl($newsElement, $routeParams, $cardRouteName);
        $listParams = new OtherListDto();
        $listParams->categoryId = $newsElement->category_id;
        $listParams->primary = $newsElement->getKey();
        $listParams->count = 3;
        $listParams->cardRoutes = new Collection([
            $newsElement->category_id => $cardRouteName
        ]);
        $listParams->pageComponentId = $pageDynComponent->id;

        $cacheId = $this->newsService->getElementCacheTag(
            'page_' . $route->pageComponent->page_id .
            '_card_' . md5($newsElement->slug ?? '')
        );

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn([
            'element' => $newsElement,
            'listParams' => $listParams,
            'template' => ElementCard::getTemplates()->first()->name
        ]);
        Cache::swap($partialCacheMock);

        $response = $this->get($newsElement->url);
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString("<h1>{$newsElement->title}</h1>", $html);
        $this->assertStringContainsString($newsElement->body, $html);
    }
}
