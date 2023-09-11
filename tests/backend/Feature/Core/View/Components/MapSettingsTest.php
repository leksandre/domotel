<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Core\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Models\Setting;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Tests\Feature\Core\View\Components\Traits\Page;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;

final class MapSettingsTest extends TestCase
{
    use Page;
    use RefreshDatabase;
    use SiteTrait;

    private SettingsService $settingsService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initSite();
        $this->settingsService = resolve(SettingsService::class);
    }

    private function createMapSettings(): Collection
    {
        $faker = Factory::create(config('app.faker_locale'));

        return new Collection([
            'service' => 'yandex',
            'yandex' => [
                'api' => $faker->uuid()
            ]
        ]);
    }

    public function testPageHasMapSettings()
    {
        $page = $this->createPage();
        $map = $this->createMapSettings();

        /** @var SettingsRepository $settingsRepo */
        $settingsRepo = resolve(SettingsRepository::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_MAP
        ]);
        $setting->value = $map;
        $settingsRepo->set($setting);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($map->get('yandex')['api'], $response->getContent());
        $this->assertTrue(Cache::tags([
            $this->settingsService->getCacheTag(
                CoreServiceProvider::MODULE_NAME,
                $this->settingsService::PARAM_MAP
            )
        ])->has('mapSettings'));
    }

    public function testPageHasMapSettingsFromCache()
    {
        $page = $this->createPage();
        $map = $this->createMapSettings();

        /** @var SettingsRepository $settingsRepo */
        $settingsRepo = resolve(SettingsRepository::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_MAP
        ]);
        $setting->value = $map;
        $settingsRepo->set($setting);

        $setting = $map;

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with('mapSettings')->andReturn($setting);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($map->get('yandex')['api'], $response->getContent());
    }
}
