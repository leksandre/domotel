<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Core\View\Components;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Kelnik\Core\Models\Setting;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\Theme\Font;
use Kelnik\Tests\Feature\Core\View\Components\Traits\Page;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Kelnik\Tests\TestFile;
use Mockery;

final class GlobalThemeTest extends TestCase
{
    use Page;
    use RefreshDatabase;
    use SiteTrait;

    private Filesystem $storage;
    private SettingsService $settingsService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initSite();
        $this->storage = Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
        $this->settingsService = resolve(SettingsService::class);
    }

    public function testPageHasGlobalThemeFromCache()
    {
        $page = $this->createPage();

        /** @var SettingsRepository $settingsRepo */
        $settingsRepo = resolve(SettingsRepository::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_COLORS
        ]);
        $setting->value = collect(['brand-text' => '#000000']);
        $settingsRepo->set($setting);

        $setting = [
            'colors' => $this->settingsService->prepareColors(collect(['brand-text' => '#000000']))
        ];

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with('theme')->andReturn($setting);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString('--color-brand-text:#000000', $response->getContent());
        $this->assertStringContainsString('--color-brand-text-rgb:0,0', $response->getContent());
    }

    public function testPageHasGlobalTheme()
    {
        $page = $this->createPage();

        /** @var SettingsRepository $settingsRepo */
        $settingsRepo = resolve(SettingsRepository::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_COLORS
        ]);
        $setting->value = collect(['brand-text' => '#000000']);
        $settingsRepo->set($setting);

        $uploaded = UploadedFile::fake();
        $fontRegular = $uploaded->create('Raleway-Regular.woff2', '15', 'font/woff2');
        $fontRegular = new TestFile($fontRegular);
        $fontRegular->setStorage($this->storage);
        $fontRegular = $fontRegular->load();

        $fontModel = new Font($fontRegular, true);

        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_FONTS
        ]);
        $setting->value = collect(['regular' => $fontModel->toArray(), 'bold' => []]);
        $settingsRepo->set($setting);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString('--color-brand-text:#000000', $response->getContent());
        $this->assertStringContainsString('--color-brand-text-rgb:0,0', $response->getContent());
        $this->assertStringContainsString($fontRegular->url, $response->getContent());
        $this->assertStringContainsString(
            '<link rel="preload" href="' . $fontRegular->url . '"',
            $response->getContent()
        );
    }
}
