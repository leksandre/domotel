<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Page\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Page\View\Components\IconBlock\IconBlock;
use Kelnik\Tests\TestFile;
use Mockery;

final class IconBlockTest extends AbstractTestComponent
{
    use RefreshDatabase;

    protected KelnikPageComponent|string $componentNamespace = IconBlock::class;

    public function testComponentReturnValidContentAlias()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText(10),
                'alias' => $faker->slug,
                'list' => []
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $viewComponent = resolve(PageService::class)->initViewComponent($page, $pageComponent);

        $this->assertTrue($viewComponent->getContentAlias() === $pageData['content']['alias']);
    }

    public function testComponentSvgReturnValidResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        gc_collect_cycles();
        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();

        $listItems = [];
        for ($i = 0; $i < 3; $i++) {
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" ' .
                'xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 10 10">' .
                '<path id="3fqaa" d="' . $faker->slug . '"></path></svg>';
            $icon = $uploaded->createWithContent($i . '.svg', $svg);
            $icon = new TestFile($icon);
            $icon->setStorage($this->storage);
            $icon = $icon->load();

            if ($i === 1) {
                $this->storage->delete($icon->physicalPath());
            }

            $listItems[] = [
                'icon' => $icon->id,
                'title' => $faker->unique()->sentence(3),
                'text' => $faker->unique()->sentence()
            ];
        }

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText(10),
                'alias' => 'icon-block',
                'list' => $listItems,
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($svg, $response->getContent());
        $this->assertStringNotContainsString($icon->url(), $response->getContent());
        $this->assertStringContainsString($listItems[0]['title'], $response->getContent());
    }

    public function testComponentReturnValidResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        gc_collect_cycles();
        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $icon = $uploaded->image('some-icon.jpg');
        $icon = new TestFile($icon);
        $icon->setStorage($this->storage);
        $icon = $icon->load();

        $listItems = [
            [
                'icon' => $icon->id,
                'title' => $faker->unique()->sentence(3),
                'text' => $faker->unique()->sentence()
            ]
        ];

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText(10),
                'alias' => 'icon-block',
                'list' => $listItems,
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($icon->name, $response->getContent());
        $this->assertStringContainsString($listItems[0]['title'], $response->getContent());
    }

    public function testComponentUseCacheOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        gc_collect_cycles();
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText(10),
                'alias' => 'icon-block',
                'list' => [],
            ]
        ];

        $pageData = collect($pageData);
        $pageComponent->data->setValue($pageData);
        $pageComponent->save();

        $pageData = collect($pageData->get('content'));

        $cacheId = resolve(PageService::class)
            ->getPageComponentCacheTag($page->getKey() . '_' . $pageComponent->getKey());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData->get('title'), $response->getContent());
    }
}
