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
use Kelnik\Page\View\Components\Gallery\Gallery;
use Kelnik\Tests\TestFile;
use Mockery;

final class GalleryTest extends AbstractTestComponent
{
    use RefreshDatabase;

    protected KelnikPageComponent|string $componentNamespace = Gallery::class;

    public function testComponentReturnValidContentAlias()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'alias' => $faker->slug,
                'slider' => [],
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

        $uploaded = UploadedFile::fake();
        $slider = [];
        for ($i = 0; $i <= 4; $i++) {
            $slide = $uploaded->image('slide' . $i . '.jpg', 1920, 1080);
            $slide = new TestFile($slide);
            $slide->setStorage($this->storage);
            $slide = $slide->load();
            $slider[] = $slide->id;
        }

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'alias' => 'gallery',
                'slider' => $slider,
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
//        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString('<picture', $response->getContent());
        $this->assertStringContainsString($slide->name, $response->getContent());
    }

    public function testComponentSuccessOnFailedAttachment()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'alias' => 'gallery',
                'slider' => [$faker->randomDigit()],
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
//        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringNotContainsString('<picture', $response->getContent());
    }

    public function testComponentUseCacheOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $slider = $sliderPicture = [];
        $code = $faker->slug(2);

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
                'picture' => resolve(CoreService::class)->hasModule('image')
                    ? Picture::init(new ImageFile($slide))
                        ->setLazyLoad(true)
                        ->setBreakpoints([1280 => 1280, 960 => 1208, 670 => 906, 320 => 632])
                        ->setImageAttribute('alt', $slide->alt ?? '')
                        ->render()
                    : null
            ];
        }
        unset($slide);

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'alias' => 'gallery',
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
//        $this->assertStringContainsString($pageData['title'], $response->getContent());
        $this->assertStringContainsString($sliderPicture->random(1)->first()['picture'], $response->getContent());
    }
}
