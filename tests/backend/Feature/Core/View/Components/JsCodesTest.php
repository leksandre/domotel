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

final class JsCodesTest extends TestCase
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

    public function testPageHasJsCodes()
    {
        $page = $this->createPage();
        $codes = $this->createCodes();

        /** @var SettingsRepository $settingsRepo */
        $settingsRepo = resolve(SettingsRepository::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_JS_CODES
        ]);
        $setting->value = $codes;
        $settingsRepo->set($setting);

        $codes = $codes->shuffle();

        $activeCode = $codes->first(static fn($el) => $el['active']);
        $inActiveCode = $codes->first(static fn($el) => !$el['active']);
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($activeCode['code'], $response->getContent());
        $this->assertStringNotContainsString($inActiveCode['code'], $response->getContent());
        $this->assertTrue(Cache::tags([
            $this->settingsService->getCacheTag(
                CoreServiceProvider::MODULE_NAME,
                $this->settingsService::PARAM_JS_CODES
            )
        ])->has('jscodes'));
    }

    public function testPageHasJsCodesFromCache()
    {
        $page = $this->createPage();
        $codes = $this->createCodes();

        /** @var SettingsRepository $settingsRepo */
        $settingsRepo = resolve(SettingsRepository::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_JS_CODES
        ]);
        $setting->value = $codes;
        $settingsRepo->set($setting);

        $setting = [
            $this->settingsService::JS_CODE_POSITION_HEAD => $codes->filter(
                fn($el) => $el['active'] && $el['section'] === $this->settingsService::JS_CODE_POSITION_HEAD
            )->pluck('code')->toArray(),

            $this->settingsService::JS_CODE_POSITION_BODY => $codes->filter(
                fn($el) => $el['active'] && $el['section'] === $this->settingsService::JS_CODE_POSITION_BODY
            )->pluck('code')->toArray()
        ];

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with('jscodes')->andReturn($setting);
        Cache::swap($partialCacheMock);

        $codes = $codes->shuffle();

        $activeCode = $codes->first(static fn($el) => $el['active']);
        $inActiveCode = $codes->first(static fn($el) => !$el['active']);
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($activeCode['code'], $response->getContent());
        $this->assertStringNotContainsString($inActiveCode['code'], $response->getContent());
    }

    private function createCodes(): Collection
    {
        $faker = Factory::create(config('app.faker_locale'));

        return new Collection([
            [
                'active' => true,
                'section' => $this->settingsService::JS_CODE_POSITION_HEAD,
                'code' => $faker->unique()->name
            ],
            [
                'active' => false,
                'section' => $this->settingsService::JS_CODE_POSITION_HEAD,
                'code' => $faker->unique()->name
            ],
            [
                'active' => true,
                'section' => $this->settingsService::JS_CODE_POSITION_BODY,
                'code' => $faker->unique()->name
            ],
            [
                'active' => false,
                'section' => $this->settingsService::JS_CODE_POSITION_BODY,
                'code' => $faker->unique()->name
            ]
        ]);
    }
}
