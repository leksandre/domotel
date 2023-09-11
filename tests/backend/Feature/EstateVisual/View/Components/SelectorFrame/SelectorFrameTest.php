<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateVisual\View\Components\SelectorFrame;

use Faker\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Kelnik\Estate\Database\Seeders\EstateSeeder;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\EstateVisual\Database\Seeders\SelectorSeeder;
use Kelnik\EstateVisual\Repositories\Contracts\SelectorRepository;
use Kelnik\EstateVisual\Services\Contracts\SelectorService;
use Kelnik\EstateVisual\View\Components\SelectorFrame\SelectorFrame;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Tests\Feature\Estate\EstatePremisesTrait;
use Kelnik\Tests\Feature\PageComponentTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;

final class SelectorFrameTest extends TestCase
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

    private function getCacheId(Page $page, PageComponent $component): string
    {
        return $this->estateService->getPremisesCacheTag(
            'page_' . $page->getKey() . '_estate_visual_frame_' . $component->getKey()
        );
    }

    public function testComponentExists()
    {
        /** @var BladeComponentRepository $componentRepository */
        $componentRepository = resolve(BladeComponentRepository::class);
        $components = $componentRepository->getAdminList()->keys()->toArray();

        $this->assertContains(SelectorFrame::initDataProvider()->getComponentCode(), $components);
    }

    public function testComponentAddedToPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, SelectorFrame::class);

        $this->assertDatabaseHas(
            $pageComponent->getTable(),
            [
                'page_id' => $page->getKey(),
                'component' => $pageComponent->component
            ]
        );
    }

    public function testShouldShowSelectorIframe()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, SelectorFrame::class);

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
            'template' => SelectorFrame::getTemplates()->last()->name,
            'frameTemplate' => SelectorFrame::getFrameTemplates()->first()->name,
            'plural' => base64_encode(
                json_encode(
                    explode('|', trans('kelnik-estate-search::front.plural.premises'))
                )
            ),
            'url' => route(
                'kelnik.estateVisual.frame',
                [
                    'id' => $selector->getKey(),
                    'cid' => $pageComponent->getKey(),
                    'iframe' => 1
                ],
                false
            )
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $cacheId = $this->getCacheId($page, $pageComponent);

        $cacheTags = [
            $this->estateService->getModuleCacheTag(),
            $this->selectorService->getCacheTag($selector),
            $this->pageService->getPageComponentCacheTag($pageComponent->id)
        ];

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($page->title, $response->getContent());
        $this->assertStringContainsString('iframe class="visual-inner__iframe', $response->getContent());
        $this->assertStringContainsString($pageData['url'], $response->getContent());
        $this->assertTrue(Cache::tags($cacheTags)->has($cacheId));
    }

    public function testShouldNotShowIframe()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, SelectorFrame::class);

        $this->seed(EstateSeeder::class);

        $faker = Factory::create(config('app.faker_locale'));
        $typeLiving = $this->getLivingTypeId();

        $pageData = [
            'title' => $faker->sentence(3),
            'selector_id' => $faker->randomDigitNotZero(),
            'types' => $typeLiving ? [$typeLiving] : [],
            'statuses' => $this->getStatusesWhenCardAvailable(),
            'frameTemplate' => SelectorFrame::getFrameTemplates()->first()->name,
            'template' => SelectorFrame::getTemplates()->last()->name
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringNotContainsString('<iframe class="visual-inner__iframe', $response->getContent());
    }

    public function testShouldShowSelectorIframeUsingCache()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, SelectorFrame::class);

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
            'frameTemplate' => SelectorFrame::getFrameTemplates()->first()->name,
            'template' => SelectorFrame::getTemplates()->last()->name,
            'plural' => base64_encode(
                json_encode(
                    explode('|', trans('kelnik-estate-search::front.plural.premises'))
                )
            ),
            'url' => route(
                'kelnik.estateVisual.frame',
                [
                    'id' => $selector->getKey(),
                    'cid' => $pageComponent->getKey(),
                    'iframe' => 1
                ],
                false
            )
        ];

        $cacheId = $this->getCacheId($page, $pageComponent);

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($page->title, $response->getContent());
        $this->assertStringContainsString('iframe class="visual-inner__iframe', $response->getContent());
        $this->assertStringContainsString($pageData['url'], $response->getContent());
    }

    public function testShouldShowSelectorSection()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, SelectorFrame::class);

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
            'template' => SelectorFrame::getTemplates()->last()->name,
            'frameTemplate' => SelectorFrame::getFrameTemplates()->first()->name,
            'plural' => base64_encode(
                json_encode(
                    explode('|', trans('kelnik-estate-search::front.plural.premises'))
                )
            ),
            'url' => route(
                'kelnik.estateVisual.frame',
                [
                    'id' => $selector->getKey(),
                    'cid' => $pageComponent->getKey(),
                    'iframe' => 1
                ],
                false
            ),
            'form' => [
                'id' => rand(1, 10),
                'text' => 'form button'
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $frameUrl = route(
            'kelnik.estateVisual.frame',
            [
                'id' => $selector->getKey(),
                'cid' => $pageComponent->getKey()
            ],
            false
        );
        $baseUrl = route(
            'kelnik.estateVisual.getData',
            [
                'id' => $selector->getKey(),
                'cid' => $pageComponent->getKey()
            ],
            false
        );

        $response = $this->get($frameUrl, ['iframe' => true]);

        $response->assertOk();
        $this->assertStringContainsString('<div id="visual"', $response->getContent());
        $this->assertStringContainsString($baseUrl, $response->getContent());
    }
}
