<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Core\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Models\Setting;
use Kelnik\Core\Platform\Services\Contracts\SettingsPlatformService;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Tests\Feature\Core\View\Components\Traits\Page;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;

final class CookieNoticeTest extends TestCase
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

    public function testPageHasCookieNotice()
    {
        $page = $this->createPage();
        $noticeData = $this->createNoticeData();

        /** @var SettingsRepository $settingsRepo */
        $settingsRepo = resolve(SettingsRepository::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_COOKIE_NOTICE
        ]);
        $setting->value = $noticeData;
        $settingsRepo->set($setting);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($noticeData['text'], $response->getContent());
        $this->assertTrue(Cache::tags([
            $this->settingsService->getCacheTag(
                CoreServiceProvider::MODULE_NAME,
                $this->settingsService::PARAM_COOKIE_NOTICE
            )
        ])->has('cookieNotice'));
    }

    public function testPageHasCookieNoticeFromCache()
    {
        $page = $this->createPage();
        $noticeData = $this->createNoticeData();

        /** @var SettingsRepository $settingsRepo */
        $settingsRepo = resolve(SettingsRepository::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_COOKIE_NOTICE
        ]);
        $setting->value = $noticeData;
        $settingsRepo->set($setting);

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with('cookieNotice')->andReturn($noticeData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($noticeData['text'], $response->getContent());
    }

    private function createNoticeData(): Collection
    {
        $faker = Factory::create(config('app.faker_locale'));

        return new Collection([
            'active' => true,
            'expired' => rand(SettingsPlatformService::EXPIRED_MIN, SettingsPlatformService::EXPIRED_MAX),
            'buttonText' => $faker->word(),
            'text' => $faker->unique()->sentence(),
            'linkText' => $faker->unique()->sentence(),
            'link' => $faker->url(),
            'popupText' => $faker->unique()->sentence()
        ]);
    }
}
