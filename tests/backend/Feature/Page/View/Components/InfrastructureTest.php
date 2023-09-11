<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Page\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Page\View\Components\Infrastructure\Infrastructure;
use Kelnik\Tests\TestFile;
use Mockery;

final class InfrastructureTest extends AbstractTestComponent
{
    use RefreshDatabase;

    protected KelnikPageComponent|string $componentNamespace = Infrastructure::class;

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
                'plan' => 0,
                'legend' => []
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

        gc_collect_cycles();
        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $plan = $uploaded->image('plan.png', 800, 800);
        $plan = new TestFile($plan);
        $plan->setStorage($this->storage);
        $plan = $plan->load();

        $legend = [];
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

            $legend[] = [
                'title' => $faker->unique()->company,
                'icon' => $icon->id,
                'sort' => 500 + $i
            ];
        }
//        unset($icon);

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText(10),
                'alias' => 'infra',
                'plan' => $plan->id,
                'legend' => $legend,
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString('<picture', $response->getContent());
        $this->assertStringContainsString($plan->name, $response->getContent());
        $this->assertStringNotContainsString($svg, $response->getContent());
        $this->assertStringContainsString($icon->url(), $response->getContent());
        $this->assertStringContainsString($legend[0]['title'], $response->getContent());
    }

    public function testComponentSvgReturnValidResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        gc_collect_cycles();
        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $icon = $uploaded->image('icon.jpg', 40, 40);
        $icon = new TestFile($icon);
        $icon->setStorage($this->storage);
        $icon = $icon->load();

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" ' .
            'xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 10 10">' .
            '<path id="3fqaa" d=""></path></svg>';
        $plan = $uploaded->createWithContent('plan.svg', $svg);
        $plan = new TestFile($plan);
        $plan->setStorage($this->storage);
        $plan = $plan->load();

        $legend = [
            [
                'title' => $faker->unique()->company,
                'icon' => $icon->id,
                'sort' => 500
            ]
        ];

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText(10),
                'alias' => 'infra',
                'plan' => $plan->id,
                'legend' => $legend,
            ]
        ];
        unset($plan);

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($icon->name, $response->getContent());
        $this->assertStringContainsString($svg, $response->getContent());
        $this->assertStringContainsString($legend[0]['title'], $response->getContent());
    }

    public function testComponentUseCacheOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        gc_collect_cycles();
        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $picture = null;
        $plan = $uploaded->image('plan.png', 800, 800);
        $plan = new TestFile($plan);
        $plan->setStorage($this->storage);
        $plan = $plan->load();
        $picture = Picture::init(new ImageFile($plan))->render();

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText(10),
                'alias' => 'infra',
                'plan' => $plan->id,
                'legend' => [],
            ]
        ];
        unset($icon);

        $pageData = collect($pageData);
        $pageComponent->data->setValue($pageData);
        $pageComponent->save();

        $pageData = collect($pageData->get('content'));
        $pageData->put('plan', $picture);

        $cacheId = resolve(PageService::class)
            ->getPageComponentCacheTag($page->getKey() . '_' . $pageComponent->getKey());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData->get('title'), $response->getContent());
        $this->assertStringContainsString($picture, $response->getContent());
    }
}
