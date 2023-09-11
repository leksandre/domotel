<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Page\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Page\View\Components\Footer\Footer;
use Kelnik\Tests\TestFile;
use Mockery;

final class FooterTest extends AbstractTestComponent
{
    use RefreshDatabase;

    protected KelnikPageComponent|string $componentNamespace = Footer::class;

    public function testComponentReturnValidResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $logo = $uploaded->image('logo.jpg', 100, 50);
        $logo = new TestFile($logo);
        $logo->setStorage($this->storage);
        $logo = $logo->load();

        $pageData = [
            'content' => [
                'logo' => $logo->id,
                'link' => $faker->url(),
                'text' => $faker->realText(),
                'copyright' => $faker->sentence(5),
                'policyText' => $faker->sentence(4),
                'policyLink' => $faker->url()
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString($logo->url(), $html);
        $this->assertStringContainsString($pageData['content']['link'], $html);
        $this->assertStringContainsString($pageData['content']['text'], $html);
        $this->assertStringContainsString($pageData['content']['copyright'], $html);
        $this->assertStringContainsString($pageData['content']['policyText'], $html);
        $this->assertStringContainsString($pageData['content']['policyLink'], $html);
    }

    public function testComponentReturnValidResultOnPageWithSvgLogo()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" ' .
            'xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 10 10">' .
            '<path id="3fqaa" d="' . $faker->slug . '"></path></svg>';
        $logo = $uploaded->createWithContent('logo.svg', $svg);
        $logo = new TestFile($logo);
        $logo->setStorage($this->storage);
        $logo = $logo->load();

        $pageData = [
            'content' => [
                'logo' => $logo->id,
                'link' => $faker->url(),
                'text' => $faker->realText(),
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString($logo->url(), $html);
        $this->assertStringContainsString($pageData['content']['link'], $html);
        $this->assertStringContainsString($pageData['content']['text'], $html);
        $this->assertStringContainsString('footer__developer-logo', $html);
    }

    public function testComponentReturnValidResultOnPageWithNoLogo()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'logo' => 0,
                'link' => $faker->url(),
                'text' => $faker->realText(),
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringNotContainsString('footer__developer-logo', $html);
        $this->assertStringNotContainsString($pageData['content']['link'], $html);
        $this->assertStringContainsString($pageData['content']['text'], $html);
    }

    public function testComponentReturnValidResultOnPageWithInvalidAttachment()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $logo = $uploaded->image('logo.jpg', 100, 50);
        $logo = new TestFile($logo);
        $logo->setStorage($this->storage);
        $logo = $logo->load();

        $logoUrl = $logo->url();
        $this->storage->delete($logo->physicalPath());

        $pageData = [
            'content' => [
                'logo' => $logo->id,
                'link' => $faker->url(),
                'text' => $faker->realText(),
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringNotContainsString($logoUrl, $html);
        $this->assertStringNotContainsString($pageData['content']['link'], $html);
        $this->assertStringNotContainsString('footer__developer-logo', $html);
    }

    public function testComponentReturnValidResultOnPageWithDismissedAttachment()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'logo' => $faker->randomDigit(),
                'link' => $faker->url(),
                'text' => $faker->realText(),
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringNotContainsString($pageData['content']['link'], $html);
        $this->assertStringNotContainsString('footer__developer-logo', $html);
    }

    public function testComponentUseCacheOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'logo' => 0,
                'link' => $faker->url(),
                'text' => $faker->realText(),
                'copyright' => $faker->sentence(5),
                'policyText' => $faker->sentence(4),
                'policyLink' => $faker->url()
            ]
        ];

        $origData = $pageData['content'];
        $pageData = collect($pageData);
        $pageComponent->data->setValue($pageData);
        $pageComponent->save();
        unset($pageData);

        $cacheId = resolve(PageService::class)
            ->getPageComponentCacheTag($page->getKey() . '_' . $pageComponent->getKey());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($origData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString($origData['text'], $html);
        $this->assertStringContainsString($origData['copyright'], $html);
        $this->assertStringContainsString($origData['policyText'], $html);
        $this->assertStringContainsString($origData['policyLink'], $html);
    }
}
