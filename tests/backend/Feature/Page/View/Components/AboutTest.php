<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Page\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\About\About;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Mockery;

final class AboutTest extends AbstractTestComponent
{
    use RefreshDatabase;

    protected KelnikPageComponent|string $componentNamespace = About::class;

    public function testComponentReturnValidContentAlias()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText,
                'alias' => $faker->slug,
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $viewComponent = resolve(PageService::class)->initViewComponent($page, $pageComponent);

        $this->assertTrue($viewComponent->getContentAlias() === $pageData['content']['alias']);
    }

    public function testComponentReturnValidResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText,
                'alias' => 'about',
                'textOnRight' => 0,
                'factoids' => [
                    ['title' => $faker->sentence(3), 'text' => $faker->sentence(5), 'sort' => 500],
                    ['title' => $faker->sentence(3), 'text' => $faker->sentence(5), 'sort' => 510]
                ],
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['text'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['factoids'][0]['title'], $response->getContent());
    }

    public function testComponentUseCacheOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'title' => $faker->company,
            'text' => $faker->realText,
            'alias' => 'about',
            'textOnRight' => 0,
            'factoids' => [
                ['title' => $faker->sentence(3), 'text' => $faker->sentence(5), 'sort' => 500],
                ['title' => $faker->sentence(3), 'text' => $faker->sentence(5), 'sort' => 510]
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
        $this->assertStringContainsString($pageData['title'], $response->getContent());
        $this->assertStringContainsString($pageData['text'], $response->getContent());
        $this->assertStringContainsString($pageData['factoids'][0]['title'], $response->getContent());
    }
}
