<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Core\View\Components;

use Illuminate\Cache\TaggedCache;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Kelnik\Core\Models\Setting;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\View\Components\FavIcon;
use Kelnik\Page\Models\Page;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Kelnik\Tests\TestFile;
use Mockery;
use Orchid\Attachment\Models\Attachment;

final class FavIconTest extends TestCase
{
    use Traits\Page;
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

    private function addFavIcon(int $width = 310): Attachment
    {
        $uploaded = UploadedFile::fake();
        $iconFile = $uploaded->image('icon.png', $width, $width);
        $iconFile = new TestFile($iconFile);
        $iconFile->setStorage($this->storage);

        return $iconFile->load();
    }

    private function getCacheTags(Page $page): TaggedCache
    {
        return Cache::tags([
            $this->settingsService->getCacheTag(
                CoreServiceProvider::MODULE_NAME,
                $this->settingsService::PARAM_COMPLEX
            )
        ]);
    }

    public function testPageDoseHasFavIcon()
    {
        $page = $this->createPage();
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringNotContainsString('<link rel="apple-touch-icon-precomposed"', $response->getContent());
        $this->assertStringNotContainsString('<link rel="apple-touch-icon"', $response->getContent());
        $this->assertStringNotContainsString('<link rel="icon" type="image/png"', $response->getContent());
        $this->assertStringNotContainsString('<meta name="msapplication-TileImage', $response->getContent());
        $this->assertStringNotContainsString('<meta name="msapplication-square', $response->getContent());
        $this->assertTrue($this->getCacheTags($page)->has('favicon'));
    }

    public function testPageHasFavIcon()
    {
        $page = $this->createPage();
        $icon = $this->addFavIcon();

        /** @var SettingsRepository $settingsRepo */
        $settingsRepo = resolve(SettingsRepository::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_COMPLEX
        ]);
        $setting->value = collect(['favicon' => $icon->getKey()]);
        $settingsRepo->set($setting);
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($icon->name, $response->getContent());
        $this->assertStringContainsString('<link rel="apple-touch-icon-precomposed"', $response->getContent());
        $this->assertStringContainsString('<link rel="apple-touch-icon"', $response->getContent());
        $this->assertStringContainsString('<link rel="icon" type="image/png"', $response->getContent());
        $this->assertStringContainsString('<meta name="msapplication-TileImage', $response->getContent());
        $this->assertStringContainsString('<meta name="msapplication-square', $response->getContent());
        $this->assertTrue($this->getCacheTags($page)->has('favicon'));
    }

    public function testPageHasSmallFavIcon()
    {
        $page = $this->createPage();
        $icon = $this->addFavIcon(180);

        /** @var SettingsRepository $settingsRepo */
        $settingsRepo = resolve(SettingsRepository::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_COMPLEX
        ]);
        $setting->value = collect(['favicon' => $icon->getKey()]);
        $settingsRepo->set($setting);
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($icon->name, $response->getContent());
        $this->assertStringContainsString('<link rel="apple-touch-icon-precomposed"', $response->getContent());
        $this->assertStringContainsString('<link rel="apple-touch-icon"', $response->getContent());
        $this->assertStringContainsString('<link rel="icon" type="image/png"', $response->getContent());
        $this->assertStringContainsString('<meta name="msapplication-TileImage', $response->getContent());
        $this->assertStringContainsString('<meta name="msapplication-square', $response->getContent());
        $this->assertStringNotContainsString('<meta name="msapplication-square310', $response->getContent());
        $this->assertStringNotContainsString(
            '<link rel="icon" type="image/png" sizes="192x192"',
            $response->getContent()
        );
        $this->assertTrue($this->getCacheTags($page)->has('favicon'));
    }

    public function testFavIconNotUseImageModule()
    {
        $coreService = Mockery::mock(CoreService::class);
        $coreService->shouldReceive('hasModule')->with('image')->andReturn(false);
        $this->app->instance(CoreService::class, $coreService);

        $page = $this->createPage();
        $icon = $this->addFavIcon(180);

        /** @var SettingsRepository $settingsRepo */
        $settingsRepo = resolve(SettingsRepository::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_COMPLEX
        ]);
        $setting->value = collect(['favicon' => $icon->getKey()]);
        $settingsRepo->set($setting);
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($icon->name, $response->getContent());
        $this->assertStringContainsString('<link rel="apple-touch-icon-precomposed"', $response->getContent());
        $this->assertStringContainsString('<link rel="apple-touch-icon"', $response->getContent());
        $this->assertStringContainsString('<link rel="icon" type="image/png"', $response->getContent());
        $this->assertStringContainsString('<meta name="msapplication-TileImage', $response->getContent());
        $this->assertStringContainsString('<meta name="msapplication-square', $response->getContent());
        $this->assertStringNotContainsString('/storage/image/w', $response->getContent());
        $this->assertStringNotContainsString('<meta name="msapplication-square310', $response->getContent());
        $this->assertStringNotContainsString(
            '<link rel="icon" type="image/png" sizes="192x192"',
            $response->getContent()
        );
        $this->assertTrue($this->getCacheTags($page)->has('favicon'));
    }

    public function testPageHasFavIconFromCache()
    {
        $page = $this->createPage();
        $icon = $this->addFavIcon();

        /** @var SettingsRepository $settingsRepo */
        $settingsRepo = resolve(SettingsRepository::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $this->settingsService::PARAM_COMPLEX
        ]);
        $setting->value = collect(['favicon' => $icon->getKey()]);
        $settingsRepo->set($setting);

        $cacheData = [
            'name' => '',
            'icon' => $icon,
            'sizes' => []
        ];

        foreach (FavIcon::SIZES as $size) {
            $cacheData['sizes'][$size] = $icon->url();
        }

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with('favicon')->andReturn($cacheData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($icon->name, $response->getContent());
        $this->assertStringContainsString('<link rel="apple-touch-icon-precomposed"', $response->getContent());
        $this->assertStringContainsString('<link rel="apple-touch-icon"', $response->getContent());
        $this->assertStringContainsString('<link rel="icon" type="image/png"', $response->getContent());
        $this->assertStringContainsString('<meta name="msapplication-TileImage', $response->getContent());
        $this->assertStringContainsString('<meta name="msapplication-square', $response->getContent());
    }
}
