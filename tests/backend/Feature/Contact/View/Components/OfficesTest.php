<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Contact\View\Components;

use Exception;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Kelnik\Contact\Models\Office;
use Kelnik\Contact\Repositories\Contracts\OfficeRepository;
use Kelnik\Contact\View\Components\Offices\Offices;
use Kelnik\Core\Map\Contracts\Coords;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Platform\Services\Contracts\SettingsPlatformService;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;

final class OfficesTest extends TestCase
{
    use RefreshDatabase;
    use SiteTrait;

    private const ITEMS_MIN = 5;
    private const ITEMS_MAX = 8;

    private Filesystem $storage;
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
        $this->faker = Factory::create(config('app.faker_locale'));
        $this->initSite();
    }

    private function createOffices(): Collection
    {
        $offices = Office::factory()->count(rand(self::ITEMS_MIN, self::ITEMS_MAX))->create();
        $activeElement = $offices->first(
            static fn(Office $el) => $el->isActive() && $el->coords->toArray() !== [0.0, 0.0]
        );
        $inActiveElement = $offices->first(static fn(Office $el) => !$el->isActive());
        $officeRepo = resolve(OfficeRepository::class);

        if (!$activeElement) {
            $el = Office::factory()->createOne(['active' => true]);
            $el->coords = resolve(Coords::class, [
                'lat' => $this->faker->latitude,
                'lng' => $this->faker->longitude
            ]);
            $officeRepo->save($el);
            $offices->add($el);
        }

        if (!$inActiveElement) {
            $el = Office::factory()->createOne(['active' => false]);
            $officeRepo->save($el);
            $offices->add($el);
        }

        return $offices;
    }

    private function createPage(): Page
    {
        return Page::factory()->createOne([
            'site_id' => $this->site->getKey(),
            'active' => true
        ]);
    }

    private function addComponentToPage(Model $page, string $componentNamespace): \Kelnik\Page\Models\PageComponent
    {
        $pageComponent = PageComponent::factory()->makeOne([
            'active' => true,
            'component' => $componentNamespace
        ]);

        if (!$page->components()->save($pageComponent)) {
            throw new Exception('Can\'t associate component to page');
        }

        return $page->components()?->first() ?? $pageComponent;
    }

    private function addMapSettings()
    {
        resolve(SettingsPlatformService::class)->saveMap(
            CoreServiceProvider::MODULE_NAME,
            [
                'service' => 'yandex',
                'yandex' => [
                    'api' => $this->faker->randomAscii()
                ]
            ]
        );
    }

    private function getDefaultPageData(): array
    {
        return [
            'content' => [
                'title' => $this->faker->company,
                'alias' => $this->faker->slug(),
            ]
        ];
    }

    public function testComponentExists()
    {
        /** @var BladeComponentRepository $componentRepository */
        $componentRepository = resolve(BladeComponentRepository::class);
        $components = $componentRepository->getAdminList()->keys()->toArray();

        $this->assertContains(Offices::initDataProvider()->getComponentCode(), $components);
    }

    public function testComponentAddedToPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, Offices::class);

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
        $pageComponent = $this->addComponentToPage($page, Offices::class);

        $pageData = $this->getDefaultPageData();

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $viewComponent = resolve(PageService::class)->initViewComponent($page, $pageComponent);

        $this->assertTrue($viewComponent->getContentAlias() === $pageData['content']['alias']);
    }

    public function testComponentReturnValidResultOnPage()
    {
        $this->addMapSettings();
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, Offices::class);
        $offices = $this->createOffices();

        $pageData = $this->getDefaultPageData();

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $activeEl = $offices->first(static fn(Office $el) => $el->isActive());
        $inActiveEl = $offices->first(static fn(Office $el) => !$el->isActive());

        if (!$activeEl->coords->lat || !$activeEl->coords->lng) {
            $activeEl->coords->lat = $this->faker->latitude;
            $activeEl->coords->lng = $this->faker->longitude;
            resolve(OfficeRepository::class)->save($activeEl);
        }

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($activeEl->title, $response->getContent());
        $this->assertTrue(!$activeEl->region || str_contains($response->getContent(), $activeEl->region));
        $this->assertTrue(!$activeEl->city || str_contains($response->getContent(), $activeEl->city));
        $this->assertTrue(!$activeEl->street || str_contains($response->getContent(), $activeEl->street));
        $this->assertTrue(!$activeEl->phone || str_contains($response->getContent(), $activeEl->phone));
        $this->assertTrue(!$activeEl->email || str_contains($response->getContent(), $activeEl->email));
        $this->assertTrue(!$activeEl->route_link || str_contains($response->getContent(), $activeEl->route_link));
        $this->assertStringNotContainsString($inActiveEl->title, $response->getContent());
        $this->assertStringContainsString('data-json', $response->getContent());
    }

    public function testComponentReturnValidResultOnPageWithoutMapSettings()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, Offices::class);
        $offices = $this->createOffices();

        $pageData = $this->getDefaultPageData();

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $activeEl = $offices->first(static fn(Office $el) => $el->isActive());
        $inActiveEl = $offices->first(static fn(Office $el) => !$el->isActive());

        $response = $this->get($page->getUrl());

        if (!str_contains($response->getContent(), 'data-json')) {
            dd($activeEl);
        }

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($activeEl->title, $response->getContent());
        $this->assertStringContainsString('data-json', $response->getContent());
        $this->assertStringNotContainsString($inActiveEl->title, $response->getContent());
    }

    public function testComponentReturnValidResultOnPageWithoutOfficeMapCoords()
    {
        $this->addMapSettings();
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, Offices::class);
        $offices = $this->createOffices();
        $repo = resolve(OfficeRepository::class);

        $offices->each(static function (Office $office) use ($repo) {
            $office->coords = resolve(Coords::class);
            $repo->save($office);
        });

        $pageData = $this->getDefaultPageData();

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $activeEl = $offices->first(static fn(Office $el) => $el->isActive());
        $inActiveEl = $offices->first(static fn(Office $el) => !$el->isActive());

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($activeEl->title, $response->getContent());
        $this->assertStringNotContainsString($inActiveEl->title, $response->getContent());
        $this->assertStringNotContainsString('data-json', $response->getContent());
    }

    public function testComponentReturnValidResultOnPageWithEmptyOfficeList()
    {
        $this->addMapSettings();
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, Offices::class);

        $pageData = $this->getDefaultPageData();

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringNotContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringNotContainsString('data-json', $response->getContent());
    }

    public function testComponentUseCacheOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, Offices::class);
        $offices = $this->createOffices();

        $pageData = $this->getDefaultPageData();

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $pageData = $pageData['content'];
        $pageData['list'] = resolve(OfficeRepository::class)->getActive();

        $cacheId = resolve(PageService::class)
            ->getPageComponentCacheTag($page->getKey() . '_' . $pageComponent->getKey());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['title'], $response->getContent());
    }
}
