<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateVisual\View\Components\Selector;

use Faker\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Kelnik\Estate\Database\Seeders\EstateSeeder;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\EstateVisual\Database\Seeders\SelectorSeeder;
use Kelnik\EstateVisual\Repositories\Contracts\SelectorRepository;
use Kelnik\EstateVisual\Services\Contracts\SelectorService;
use Kelnik\EstateVisual\View\Components\Selector\RouteProvider;
use Kelnik\EstateVisual\View\Components\Selector\Selector;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Tests\Feature\Estate\EstatePremisesTrait;
use Kelnik\Tests\Feature\PageComponentTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;
use Symfony\Component\HttpFoundation\Response;

final class SelectorTest extends TestCase
{
    use EstatePremisesTrait;
    use RefreshDatabase;
    use PageComponentTrait;
    use SiteTrait;

    private Filesystem $storage;
    private EstateService $estateService;
    private SelectorService $selectorService;
    private PageService $pageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
        $this->estateService = resolve(EstateService::class);
        $this->selectorService = resolve(SelectorService::class);
        $this->pageService = resolve(PageService::class);
        $this->initSite();
    }

    private function getCacheId(Page $page): string
    {
        return $this->estateService->getPremisesCacheTag('page_' . $page->getKey() . '_estate_visual');
    }

    public function testComponentExists()
    {
        /** @var BladeComponentRepository $componentRepository */
        $componentRepository = resolve(BladeComponentRepository::class);
        $components = $componentRepository->getAdminList()->keys()->toArray();

        $this->assertContains(Selector::initDataProvider()->getComponentCode(), $components);
    }

    public function testComponentAddedToPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, Selector::class);

        $this->assertDatabaseHas(
            $pageComponent->getTable(),
            [
                'page_id' => $page->getKey(),
                'component' => $pageComponent->component
            ]
        );
    }

    public function testShouldShowSelectorSection()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, Selector::class);

        $this->seed(EstateSeeder::class);
        $this->seed(SelectorSeeder::class);

        $faker = Factory::create(config('app.faker_locale'));
        $typeLiving = $this->getLivingTypeId();
        $selector = resolve(SelectorRepository::class)->getActiveFirst();

        $pageData = [
            'title' => $faker->sentence(3),
            'selector_id' => $selector->getKey(),
            'types' => $typeLiving ? [$typeLiving] : [],
            'statuses' => $this->getStatusesWhenCardAvailable(),
            'template' => Selector::getTemplates()->last()->name,
            'form' => [
                'id' => rand(1, 10),
                'text' => $faker->title
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $pageComponent->load('routes');

        /**
         * @var RouteProvider $routeProvider
         * @var PageLinkService $pageLinkService
         */
        $routeProvider = $pageComponent->component::initRouteProvider($page, $pageComponent);
        $pageLinkService = resolve(PageLinkService::class);

        $pageLinkService->syncPageComponentRoutes(
            $pageComponent,
            $routeProvider->makeRoutesByParams([])
        );

        $this->app['router']->getRoutes()->refreshNameLookups();
        $visualRouteName = $pageLinkService->getPageComponentRouteName($pageComponent->routes()->get()?->first());

        $cacheId = $this->getCacheId($page);

        $cacheTags = [
            $this->estateService->getModuleCacheTag(),
            $this->selectorService->getCacheTag($selector),
            $this->pageService->getPageComponentCacheTag($pageComponent->id)
        ];

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($page->title, $response->getContent());
        $this->assertStringContainsString('<div id="visual"', $response->getContent());
        $this->assertTrue(Route::getRoutes()->hasNamedRoute($visualRouteName));
        $this->assertTrue(Cache::tags($cacheTags)->has($cacheId));
    }

    public function testShouldNotShowSelector()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, Selector::class);

        $this->seed(EstateSeeder::class);

        $faker = Factory::create(config('app.faker_locale'));
        $typeLiving = $this->getLivingTypeId();

        $pageData = [
            'title' => $faker->sentence(3),
            'selector_id' => $faker->randomDigitNotZero(),
            'types' => $typeLiving ? [$typeLiving] : [],
            'statuses' => $this->getStatusesWhenCardAvailable(),
            'template' => Selector::getTemplates()->last()->name
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldShowSelectorSectionUsingCache()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, Selector::class);

        $this->seed(EstateSeeder::class);
        $this->seed(SelectorSeeder::class);

        $faker = Factory::create(config('app.faker_locale'));
        $typeLiving = $this->getLivingTypeId();
        $selector = resolve(SelectorRepository::class)->getActiveFirst();

        $types = $typeLiving ? [$typeLiving] : [];

        $pageData = [
            'title' => $faker->sentence(1),
            'selector_id' => $selector->getKey(),
            'types' => json_encode($types),
            'statuses' => json_encode($this->getStatusesWhenCardAvailable()),
            'template' => Selector::getTemplates()->last()->name,
            'plural' => base64_encode(
                json_encode(
                    explode('|', trans('kelnik-estate-search::front.plural.premises'))
                )
            ),
            'url' => route(
                'kelnik.estateVisual.getData',
                ['id' => $selector->getKey(), 'cid' => rand(1, 10)]
            ),
            'baseUrl' => $page->getUrl(),
            'assets' => [
                'css' => [],
                'js' => []
            ]
        ];

        $cacheId = $this->getCacheId($page);

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($page->title, $response->getContent());
        $this->assertStringContainsString('<div id="visual"', $response->getContent());
        $this->assertStringContainsString($pageData['url'], $response->getContent());
        $this->assertStringContainsString($pageData['baseUrl'], $response->getContent());
    }
}
