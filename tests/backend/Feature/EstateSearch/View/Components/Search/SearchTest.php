<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateSearch\View\Components\Search;

use Faker\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Kelnik\Estate\Database\Seeders\EstateSeeder;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\EstateSearch\View\Components\Search\Search;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Tests\Feature\Estate\EstatePremisesTrait;
use Kelnik\Tests\Feature\PageComponentTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;

final class SearchTest extends TestCase
{
    use EstatePremisesTrait;
    use PageComponentTrait;
    use RefreshDatabase;
    use SiteTrait;

    private Filesystem $storage;
    private EstateService $estateService;
    private PageService $pageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
        $this->estateService = resolve(EstateService::class);
        $this->pageService = resolve(PageService::class);
        $this->initSite();
    }

    public function testComponentExists()
    {
        /** @var BladeComponentRepository $componentRepository */
        $componentRepository = resolve(BladeComponentRepository::class);
        $components = $componentRepository->getAdminList()->keys()->toArray();

        $this->assertContains(Search::initDataProvider()->getComponentCode(), $components);
    }

    public function testComponentAddedToPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, Search::class);

        $this->assertDatabaseHas(
            $pageComponent->getTable(),
            [
                'page_id' => $page->getKey(),
                'component' => $pageComponent->component
            ]
        );
    }

    public function testShouldShowSearchSection()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, Search::class);
        $this->seed(EstateSeeder::class);

        $faker = Factory::create(config('app.faker_locale'));

        $config = $this->makeConfig();
        $pageData = [
            'title' => $faker->sentence(3),
            'types' => $config->types,
            'statuses' => $config->statuses,
            'filters' => $config->filters,
            'orders' => $config->orders,
            'template' => Search::getTemplates()->last()->name,
            'form_id' => rand(1, 10)
        ];
        unset($config);

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $cacheId = $this->estateService->getPremisesCacheTag('page_' . $page->getKey() . '_estate_search');

        $cacheTags = [
            $this->estateService->getModuleCacheTag(),
            $this->pageService->getPageComponentCacheTag($pageComponent->id)
        ];

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($page->title, $response->getContent());
        $this->assertStringContainsString('<div id="parametric"', $response->getContent());

        $this->assertTrue(Cache::tags($cacheTags)->has($cacheId));
    }

    public function testShouldShowSearchSectionUsingCache()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, Search::class);

        $faker = Factory::create(config('app.faker_locale'));
        $config = $this->makeConfig();
        $pageData = [
            'title' => $faker->sentence(3),
            'types' => $config->types,
            'statuses' => $config->statuses,
            'filters' => $config->filters,
            'orders' => $config->orders,
            'template' => Search::getTemplates()->last()->name,
            'plural' => explode('|', trans('kelnik-estate-search::front.pluralDefault'))
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $pageData['url'] = route('kelnik.estateSearch.results', ['cid' => rand(1, 10)], false);
        $pageData['baseUrl'] = $page->getUrl();
        $pageData['assets'] = [
            'css' => [],
            'js' => []
        ];

        $pageData['searchParamId'] = encrypt($pageComponent->getKey());
        $pageData['plural'] = encrypt(json_encode($pageData['plural']));


        $cacheId = $this->estateService->getPremisesCacheTag('page_' . $page->getKey() . '_estate_search');

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($page->title, $response->getContent());
        $this->assertStringContainsString('<div id="parametric"', $response->getContent());
        $this->assertStringContainsString($pageData['url'], $response->getContent());
        $this->assertStringContainsString($pageData['baseUrl'], $response->getContent());
    }
}
