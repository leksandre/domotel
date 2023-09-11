<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\News\View\Components;

use Exception;
use Faker\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Kelnik\News\Models\Element;
use Kelnik\News\Services\Contracts\NewsService;
use Kelnik\News\View\Components\StaticList\StaticList;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Tests\Feature\PageComponentTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Kelnik\Tests\TestFile;
use Mockery;

final class StaticListTest extends TestCase
{
    use RefreshDatabase;
    use PageComponentTrait;
    use NewsTrait;
    use SiteTrait;

    private const ITEMS_MIN = 3;
    private const ITEMS_MAX = 15;

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

    public function testComponentExists()
    {
        /** @var BladeComponentRepository $componentRepository */
        $componentRepository = resolve(BladeComponentRepository::class);
        $components = $componentRepository->getAdminList()->keys()->toArray();

        $this->assertContains(StaticList::initDataProvider()->getComponentCode(), $components);
    }

    public function testComponentAddedToPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, StaticList::class);

        $this->assertDatabaseHas(
            $pageComponent->getTable(),
            [
                'page_id' => $page->getKey(),
                'component' => $pageComponent->component
            ]
        );
    }

    public function testComponentReturnValidContentAlias()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, StaticList::class);
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->sentence(3),
                'alias' => $faker->slug,
                'categories' => [],
                'limit' => 5,
                'pageId' => 0
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $viewComponent = resolve(PageService::class)->initViewComponent($page, $pageComponent);

        $this->assertTrue($viewComponent->getContentAlias() === $pageData['content']['alias']);
    }

    /** @throws Exception */
    public function testComponentReturnValidResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, StaticList::class);

        $faker = Factory::create(config('app.faker_locale'));

        $news = $this->createNewsElements();
        $category = $news->first()->category;
        $limit = rand(self::ITEMS_MIN, self::ITEMS_MAX);

        // Create card page
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPageForCategory($category);

        $pageData = [
            'content' => [
                'title' => $faker->sentence(3),
                'alias' => 'news',
                'categories' => [$category->getKey()],
                'limit' => $limit
            ],
            'template' => StaticList::getTemplates()->last()->name
        ];

        // Set preview image on first news element
        $uploaded = UploadedFile::fake();
        $img = $uploaded->image('news-preview-image.jpg', 800, 600);
        $img = new TestFile($img);
        $img->setStorage($this->storage);
        $img = $img->load();

        $newsEl = $news->sortByDesc(static fn($el) => $el->publish_date)->first();
        $newsEl->preview_image = $img->id;
        $newsEl->save();
        unset($img, $newsEl);

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $this->app['router']->getRoutes()->refreshNameLookups();
        $cacheId = $this->pageService->getPageComponentCacheTag($page->id . '_' . $pageComponent->id);

        $response = $this->get($page->getUrl());
        $html = $response->getContent();

        $news = $news->sortByDesc(static fn($el) => $el->publish_date)->slice(0, $limit);
        $newsExistsOnPage = true;
        $news->each(static function (Element $el) use ($html, &$newsExistsOnPage) {
            if (
                !str_contains($html, $el->slug)
                || !str_contains($html, $el->title)
            ) {
                $newsExistsOnPage = false;
                return false;
            }
        });

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $html);
        $this->assertTrue($newsExistsOnPage);
        $this->assertTrue(Route::getRoutes()->hasNamedRoute($cardRouteName));
        $this->assertTrue(Cache::tags([
            $this->pageService->getPageComponentCacheTag($pageComponent->id),
            $this->pageService->getDynComponentCacheTag($cardRouteName),
            $this->newsService->getCategoryCacheTag($category->getKey()),
        ])->has($cacheId));
    }

    public function testComponentUseCacheOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, StaticList::class);

        $faker = Factory::create(config('app.faker_locale'));
        $news = $this->createNewsElements();
        $category = $news->first()->category;
        $limit = rand(self::ITEMS_MIN, self::ITEMS_MAX);

        $pageData = [
            'content' => [
                'title' => $faker->sentence(3),
                'alias' => 'news',
                'categories' => [$category->getKey()],
                'limit' => $limit,
                'template' => StaticList::getTemplates()->first()->name
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $pageData = $pageData['content'];
        $news = $news->sortByDesc(static fn($el) => $el->publish_date)->slice(0, $limit);
        $news = $this->newsService->prepareElements($news, new Collection());
        $pageData['list'] = $news;

        $cacheId = resolve(PageService::class)
            ->getPageComponentCacheTag($page->getKey() . '_' . $pageComponent->getKey());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString($pageData['title'], $html);
    }
}
