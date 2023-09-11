<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Page\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Page\View\Components\Usp\Usp;
use Kelnik\Tests\TestFile;
use Mockery;

final class UspTest extends AbstractTestComponent
{
    use RefreshDatabase;

    protected KelnikPageComponent|string $componentNamespace = Usp::class;

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
                'textOnLeft' => 0,
                'icon' => [0],
                'options' => [],
                'slider' => []
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
        $slider = [];
        for ($i = 0; $i <= 4; $i++) {
            $slide = $uploaded->image('slide' . $i . '.jpg', 1920, 1080);
            $slide = new TestFile($slide);
            $slide->setStorage($this->storage);
            $slide = $slide->load();
            $slider[] = $slide->id;
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" ' .
            'xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 10 10">' .
            '<path id="3fqaa" d=""></path></svg>';
        $icon = $uploaded->createWithContent('1.svg', $svg);
        $icon = new TestFile($icon);
        $icon->setStorage($this->storage);
        $icon = $icon->load();

        $options = [
            [
                'title' => $faker->unique()->company,
                'sort' => 500
            ],
            [
                'title' => $faker->unique()->company,
                'sort' => 510
            ]
        ];

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText(10),
                'alias' => 'usp',
                'textOnLeft' => 0,
                'icon' => [$icon->id],
                'options' => $options,
                'slider' => $slider,
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString('<picture', $response->getContent());
        $this->assertStringContainsString($slide->name, $response->getContent());
        $this->assertStringNotContainsString($svg, $response->getContent());
        $this->assertStringContainsString($icon->url(), $response->getContent());
        $this->assertStringContainsString($options[0]['title'], $response->getContent());
    }

    public function testComponentSuccessOnFailedAttachment()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        gc_collect_cycles();
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText(10),
                'alias' => 'usp',
                'textOnLeft' => 0,
                'icon' => 0,
                'slider' => [$faker->randomDigit()],
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringNotContainsString('<picture', $response->getContent());
    }

    public function testComponentUseCacheOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        gc_collect_cycles();
        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $slider = $sliderPicture = [];
        $code = $faker->slug(2);
        $hasImageModule = resolve(CoreService::class)->hasModule('image');

        for ($i = 0; $i <= 4; $i++) {
            $slide = $uploaded->image('slide' . $i . '.jpg', 1920, 1080);
            $slide = new TestFile($slide);
            $slide->setStorage($this->storage);
            $slide = $slide->load();
            $slider[] = $slide->id;
            $sliderPicture[] = [
                'id' => $slide->getKey(),
                'code' => $code,
                'url' => $slide->url(),
                'alt' => $slide->alt,
                'description' => $slide->description,
                'picture' => $hasImageModule
                    ? Picture::init(new ImageFile($slide))
                        ->setLazyLoad(true)
                        ->setBreakpoints([1280 => 720, 960 => 1066, 670 => 800, 320 => 632])
                        ->setImageAttribute('alt', $slide->alt ?? '')
                        ->render()
                    : null
            ];
        }
        unset($slide);

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText(10),
                'alias' => 'usp',
                'textOnLeft' => 0,
                'icon' => 0,
                'slider' => $slider,
            ]
        ];

        $pageData = collect($pageData);
        $pageComponent->data->setValue($pageData);
        $pageComponent->save();

        $pageData = collect($pageData->get('content'));
        $sliderPicture = collect($sliderPicture);
        $pageData->put('slider', $sliderPicture);

        $cacheId = resolve(PageService::class)
            ->getPageComponentCacheTag($page->getKey() . '_' . $pageComponent->getKey());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['title'], $response->getContent());
        $this->assertStringContainsString($sliderPicture->random(1)->first()['picture'], $response->getContent());
    }
}
