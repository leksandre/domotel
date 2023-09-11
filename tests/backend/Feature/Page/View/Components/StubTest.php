<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Page\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Page\View\Components\Stub\Stub;
use Kelnik\Tests\TestFile;
use Mockery;

final class StubTest extends AbstractTestComponent
{
    use RefreshDatabase;

    protected KelnikPageComponent|string $componentNamespace = Stub::class;

    public function testComponentReturnValidResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $logo = $uploaded->image('logo.png', 396, 81);
        $logo = new TestFile($logo);
        $logo->setStorage($this->storage);
        $logo = $logo->load();

        $bg = $uploaded->image('bg.jpg', 1920, 1080);
        $bg = new TestFile($bg);
        $bg->setStorage($this->storage);
        $bg = $bg->load();

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText(),
                'phone' => $faker->phoneNumber,
                'email' => $faker->companyEmail,
                'logo' => $logo->id,
                'background' => $bg->id
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['text'], $response->getContent());
    }

    public function testComponentUseCacheOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText(),
                'phone' => $faker->phoneNumber,
                'phoneLink' => 123,
                'email' => $faker->companyEmail,
                'logo' => 0,
                'background' => 0
            ]
        ];

        $pageData = collect($pageData);
        $pageComponent->data->setValue($pageData);
        $pageComponent->save();

        $cacheId = resolve(PageService::class)
            ->getPageComponentCacheTag($page->getKey() . '_' . $pageComponent->getKey());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['text'], $response->getContent());
    }
}
