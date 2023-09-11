<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Page\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Helpers\PhoneHelper;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Platform\Services\Contracts\SettingsPlatformService;
use Kelnik\Form\Models\Form;
use Kelnik\Menu\Models\Menu;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Page\View\Components\Header\Header;
use Kelnik\Tests\TestFile;
use Mockery;

final class HeaderTest extends AbstractTestComponent
{
    use RefreshDatabase;

    protected KelnikPageComponent|string $componentNamespace = Header::class;

    private function createMenu(): Menu
    {
        return Menu::factory()->hasItems(10)->createOne(['active' => true]);
    }

    private function createForm(): Form
    {
        return Form::factory()->createOne(['active' => true]);
    }

    public function testComponentReturnValidResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $logoLight = $uploaded->image('logo.jpg', 100, 50);
        $logoLight = new TestFile($logoLight);
        $logoLight->setStorage($this->storage);
        $logoLight = $logoLight->load();

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" ' .
            'xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 10 10">' .
            '<path id="3fqaa" d="' . $faker->slug . '"></path></svg>';
        $logoDark = $uploaded->createWithContent('logo.svg', $svg);
        $logoDark = new TestFile($logoDark);
        $logoDark->setStorage($this->storage);
        $logoDark = $logoDark->load();

        $coreService = resolve(CoreService::class);

        $menu = $coreService->hasModule('menu')
            ? $this->createMenu()
            : null;
        $form = $coreService->hasModule('form')
            ? $this->createForm()
            : null;

        $pageData = [
            'content' => [
                'logoLight' => $logoLight->id,
                'logoDark' => $logoDark->id,
                'logoHeight' => Header::LOGO_HEIGHT_MIN,
                'style' => null,
                'callbackButton' => [
                    'text' => $faker->sentence(3),
                    'form_id' => $form?->getKey() ?? 0,
                ],
                'phone' => $faker->phoneNumber,
                'menu' => [
                    'desktop' => [
                        'id' => $menu?->getKey() ?? 0,
                    ],
                    'mobile' => [
                        'id' => 0
                    ]
                ]
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString($logoLight->url(), $html);
        $this->assertStringContainsString($logoDark->url(), $html);
        $this->assertStringContainsString($logoDark->url() . '" width="10" height="10"', $html);
        $this->assertStringContainsString($pageData['content']['phone'], $html);
        $this->assertTrue(!$menu || str_contains($html, 'header__navigation'));
        $this->assertTrue(!$form || str_contains($html, $form->title));
    }

    public function testComponentReturnValidResultOnPageWithInvalidAttachments()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $logoLight = $uploaded->image('logo.jpg', 100, 50);
        $logoLight = new TestFile($logoLight);
        $logoLight->setStorage($this->storage);
        $logoLight = $logoLight->load();

        $logoUrl = $logoLight->url();
        $this->storage->delete($logoLight->physicalPath());

        $pageData = [
            'content' => [
                'logoLight' => $logoLight->id,
                'logoDark' => $faker->randomDigit(),
                'style' => null,
                'callbackButton' => [],
                'phone' => null,
                'menu' => []
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringNotContainsString($logoUrl, $html);
        $this->assertStringNotContainsString('class="header__logo-light"', $html);
        $this->assertStringNotContainsString('class="header__logo-dark"', $html);
    }

    public function testComponentReturnValidResultOnPageWithComplexGlobalSettings()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $logoLight = $uploaded->image('logo.jpg', 100, 50);
        $logoLight = new TestFile($logoLight);
        $logoLight->setStorage($this->storage);
        $logoLight = $logoLight->load();

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" ' .
            'xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 10 10">' .
            '<path id="3fqaa" d="' . $faker->slug . '"></path></svg>';
        $logoDark = $uploaded->createWithContent('logo.svg', $svg);
        $logoDark = new TestFile($logoDark);
        $logoDark->setStorage($this->storage);
        $logoDark = $logoDark->load();

        resolve(SettingsPlatformService::class)->saveComplex(
            CoreServiceProvider::MODULE_NAME,
            $complexData = [
                'name' => $faker->company(),
                'phone' => $faker->phoneNumber(),
                'email' => $faker->email(),
                'logoLight' => $logoLight->id,
                'logoDark' => $logoDark->id
            ]
        );

        $pageData = [
            'content' => [
                'logoLight' => 0,
                'logoDark' => 0,
                'style' => null,
                'callbackButton' => [],
                'phone' => null,
                'menu' => []
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString($logoLight->url(), $html);
        $this->assertStringContainsString($logoDark->url(), $html);
        $this->assertStringContainsString($complexData['name'], $html);
        $this->assertStringContainsString($complexData['phone'], $html);
    }

    public function testComponentUseCacheOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'logoLight' => 0,
                'logoDark' => 0,
                'style' => null,
                'callbackButton' => [],
                'phone' => $faker->phoneNumber,
                'menu' => []
            ]
        ];

        $origData = $pageData['content'];
        $pageData = collect($pageData);
        $pageComponent->data->setValue($pageData);
        $pageComponent->save();

        $pageData = [
            'phone' => $origData['phone'],
            'phoneLink' => PhoneHelper::normalize($origData['phone']),
            'callbackButton' => $origData['callbackButton'],
            'homeLink' => '/',
            'complexName' => $faker->sentence()
        ];
        unset($origData);

        $cacheId = resolve(PageService::class)
            ->getPageComponentCacheTag($page->getKey() . '_' . $pageComponent->getKey());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString($pageData['phone'], $html);
    }
}
