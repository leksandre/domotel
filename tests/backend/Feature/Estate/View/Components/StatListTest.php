<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Estate\View\Components;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Kelnik\Estate\Database\Seeders\StatSeeder;
use Kelnik\Estate\Repositories\Contracts\StatRepository;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\Estate\View\Components\StatList\StatList;
use Kelnik\Estate\View\Components\StatList\StatListDto;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Tests\Feature\Estate\View\Components\Traits\EstatePageComponentTrait;
use Kelnik\Tests\Feature\PageComponentTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;

final class StatListTest extends TestCase
{
    use EstatePageComponentTrait;
    use PageComponentTrait;
    use RefreshDatabase;
    use SiteTrait;

    private Filesystem $storage;
    private EstateService $estateService;
    private PageService $pageService;
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
        $this->estateService = resolve(EstateService::class);
        $this->pageService = resolve(PageService::class);
        $this->seed(StatSeeder::class);
        $this->initSite();

        $this->faker = Factory::create(config('app.faker_locale'));
    }

    private function makeTypesArr(): array
    {
        $typeIds = [
            StatSeeder::PREMISES_TYPE_STUDIO,
            StatSeeder::PREMISES_TYPE_1ROOM,
            StatSeeder::PREMISES_TYPE_2ROOM,
        ];
        $types = [];
        $activeEl = [];
        $inactiveEl = [];

        foreach ($typeIds as $type) {
            $type = [
                'id' => $type,
                'active' => $this->faker->boolean,
                'title' => $this->faker->unique()->sentence(2),
                'url' => $this->faker->unique()->url
            ];

            if ($type['active'] && !$activeEl) {
                $activeEl = $type;
            } elseif (!$type['active'] && !$inactiveEl) {
                $inactiveEl = $type;
            }

            $types[$type['id']] = $type;
        }

        if (!$activeEl) {
            $activeEl = [
                'id' => StatSeeder::PREMISES_TYPE_3ROOM,
                'active' => true,
                'title' => $this->faker->unique()->sentence(2),
                'url' => $this->faker->unique()->url
            ];
            $types[$activeEl['id']] = $activeEl;
        }

        if (!$inactiveEl) {
            $inactiveEl = [
                'id' => StatSeeder::PREMISES_TYPE_4ROOM,
                'active' => false,
                'title' => $this->faker->unique()->sentence(2),
                'url' => $this->faker->unique()->url
            ];
            $types[$inactiveEl['id']] = $inactiveEl;
        }

        return [$types, $activeEl, $inactiveEl];
    }

    public function testStatIsShowed()
    {
        [$types, $activeEl, $inactiveEl] = $this->makeTypesArr();
        $stat = resolve(StatRepository::class)->getStatByTypes(array_column($types, 'id'));

        $componentDto = new StatListDto();
        $componentDto->types = array_filter($types, static fn(array $el) => $el['active']);
        $componentDto->pageId = $this->faker->randomDigitNotZero();
        $componentDto->pageComponentId = $this->faker->randomDigitNotZero();
        $component = new StatList($componentDto);

        $cacheTags = [
            $this->estateService->getModuleCacheTag(),
            $this->pageService->getPageCacheTag($componentDto->pageId),
            $this->pageService->getPageComponentCacheTag($componentDto->pageComponentId)
        ];
        $cacheId = $this->estateService->getModuleCacheTag() . '_stat_' . $componentDto->pageComponentId;

        $html = $component->render()?->render() ?? '';

        $activeEl['stat'] = $stat[$activeEl['id']];

        $this->assertStringContainsString($activeEl['title'], $html);
        $this->assertStringContainsString($activeEl['url'], $html);
        $this->assertStringContainsString((string)$activeEl['stat']['area_min'], $html);
        $this->assertStringNotContainsString($inactiveEl['title'], $html);
        $this->assertTrue(Cache::tags($cacheTags)->has($cacheId));
    }

    public function testInvalidStatValueIsNotShowed()
    {
        [$types, $activeEl, $inactiveEl] = $this->makeTypesArr();
        $invalidTypeId = rand(StatSeeder::PREMISES_TYPE_4ROOM + 1, 1000);

        $invalidType = $types[$invalidTypeId] = [
            'id' => $invalidTypeId,
            'active' => true,
            'title' => $this->faker->unique()->sentence(2),
            'url' => $this->faker->unique()->url
        ];

        $stat = resolve(StatRepository::class)->getStatByTypes(array_column($types, 'id'));

        $componentDto = new StatListDto();
        $componentDto->types = array_filter($types, static fn(array $el) => $el['active']);
        $componentDto->pageComponentId = $this->faker->randomDigitNotZero();
        $component = new StatList($componentDto);

        $html = $component->render()?->render() ?? '';

        $this->assertStringContainsString($activeEl['title'], $html);
        $this->assertStringContainsString($activeEl['url'], $html);
        $this->assertTrue(!isset($stat[$invalidTypeId]));
        $this->assertStringNotContainsString($invalidType['title'], $html);
        $this->assertStringNotContainsString($invalidType['url'], $html);
    }

    public function testStatShowedOnPageUsingCache()
    {
        [$types, $activeEl, $inactiveEl] = $this->makeTypesArr();

        $componentDto = new StatListDto();
        $componentDto->types = array_filter($types, static fn(array $el) => $el['active']);
        $componentDto->pageComponentId = $this->faker->randomDigitNotZero();
        $component = new StatList($componentDto);

        $cacheId = $this->estateService->getModuleCacheTag() . '_stat_' . $componentDto->pageComponentId;

        $stat = resolve(StatRepository::class)->getStatByTypes(array_column($types, 'id'));
        $cacheRes = [];

        foreach ($types as $type) {
            if (!isset($stat[$type['id']])) {
                continue;
            }
            $type['priceMin'] = Arr::get($stat, $type['id'] . '.price_min', 0);
            $type['areaMin'] = Arr::get($stat, $type['id'] . '.area_min', 0);
            $cacheRes['list'][$type['id']] = $type;
        }

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($cacheRes);
        Cache::swap($partialCacheMock);

        $html = $component->render()?->render() ?? '';

        $this->assertStringContainsString($activeEl['title'], $html);
        $this->assertStringContainsString($activeEl['url'], $html);
    }
}
