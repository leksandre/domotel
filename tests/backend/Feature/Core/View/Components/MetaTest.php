<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Core\View\Components;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Models\Setting;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Tests\Feature\Core\View\Components\Traits\Page;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;

final class MetaTest extends TestCase
{
    use Page;
    use RefreshDatabase;
    use SiteTrait;

    private const DEFAULT_HEX_COLOR = '#95d0a1';
    private const COLOR_NAME = 'brand-base';
    private Generator $faker;
    private SettingsService $settingsService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initSite();
        $this->faker = Factory::create(config('app.faker_locale'));
        $this->settingsService = resolve(SettingsService::class);
    }

    private function getHexColor(): string
    {
        for ($i = 0; $i <= 10; $i++) {
            $color = $this->faker->hexColor;
            if ($color !== self::DEFAULT_HEX_COLOR) {
                return $color;
            }
        }

        return self::DEFAULT_HEX_COLOR;
    }

    public function testPageHasMetaTags()
    {
        $page = $this->createPage();
        $color = $this->getHexColor();
        $complexName = $this->faker->company;

        /** @var SettingsRepository $settingsRepo */
        $settingsRepo = resolve(SettingsRepository::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_COLORS
        ]);
        $setting->value = collect([self::COLOR_NAME => $color]);
        $settingsRepo->set($setting);

        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_COMPLEX
        ]);
        $setting->value = collect(['name' => $complexName]);
        $settingsRepo->set($setting);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString(
            '<meta name="theme-color" content="' . $color . '">',
            $response->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="msapplication-TileColor" content="' . $color . '">',
            $response->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="application-name" content="' . htmlentities($complexName, ENT_QUOTES, 'UTF-8') . '">',
            $response->getContent()
        );
    }

    public function testPageHasDefaultMetaTags()
    {
        $page = $this->createPage();
        $color = self::DEFAULT_HEX_COLOR;

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString(
            '<meta name="theme-color" content="' . $color . '">',
            $response->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="msapplication-TileColor" content="' . $color . '">',
            $response->getContent()
        );
        $this->assertStringNotContainsString('<meta name="application-name" content="', $response->getContent());
    }

    public function testPageHasMetaTagsFromCache()
    {
        $page = $this->createPage();
        $color = $this->getHexColor();
        $complexName = $this->faker->company;

        /** @var SettingsRepository $settingsRepo */
        $settingsRepo = resolve(SettingsRepository::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_COLORS
        ]);
        $setting->value = collect([self::COLOR_NAME => $color]);
        $settingsRepo->set($setting);

        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_COMPLEX
        ]);
        $setting->value = collect(['name' => $complexName]);
        $settingsRepo->set($setting);

        $setting = [
            'color' => $color,
            'name' => $complexName
        ];

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with('meta')->andReturn($setting);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString(
            '<meta name="theme-color" content="' . $color . '">',
            $response->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="msapplication-TileColor" content="' . $color . '">',
            $response->getContent()
        );
        $this->assertStringContainsString(
            '<meta name="application-name" content="' . htmlentities($complexName, ENT_QUOTES, 'UTF-8') . '">',
            $response->getContent()
        );
    }
}
