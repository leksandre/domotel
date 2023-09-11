<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Page\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Kelnik\Estate\Database\Seeders\StatSeeder;
use Kelnik\News\Models\Category;
use Kelnik\News\Models\Element;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Page\View\Components\FirstScreen\FirstScreen;
use Kelnik\Tests\TestFile;
use Mockery;

final class FirstScreenTest extends AbstractTestComponent
{
    use RefreshDatabase;

    protected KelnikPageComponent|string $componentNamespace = FirstScreen::class;

    public function testComponentReturnValidContentAlias()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'slogan' => $faker->company,
                'complexName' => $faker->company,
                'alias' => $faker->slug,
                'action' => ['id' => 0, 'buttonText' => '', 'buttonLink' => ''],
                'advantages' => [
                    ['title' => $faker->word, 'sort' => 500]
                ],
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
        unset($slide);

        $pageData = [
            'content' => [
                'slogan' => $faker->company,
                'complexName' => $faker->company,
                'alias' => 'home',
                'action' => ['id' => 0, 'buttonText' => '', 'buttonLink' => ''],
                'advantages' => [
                    ['title' => $faker->word, 'sort' => 500]
                ],
                'slider' => $slider,
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['slogan'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['complexName'], $response->getContent());
        $this->assertStringContainsString('<picture', $response->getContent());
    }

    public function testComponentSuccessOnFailedAttachment()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'slogan' => $faker->company,
                'complexName' => $faker->company,
                'alias' => 'home',
                'action' => ['id' => 0, 'buttonText' => '', 'buttonLink' => ''],
                'advantages' => [
                    ['title' => $faker->word, 'sort' => 500]
                ],
                'slider' => [$faker->randomDigit()],
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['slogan'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['complexName'], $response->getContent());
        $this->assertStringNotContainsString('<picture', $response->getContent());
        $this->assertStringNotContainsString('first-screen__lead-container', $response->getContent());
    }

    public function testComponentUseCacheOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'slogan' => $faker->company,
            'complexName' => $faker->company,
            'alias' => 'home',
            'actionId' => 0,
            'advantages' => [
                ['title' => $faker->word, 'sort' => 500]
            ],
            'slider' => [],
            'fullHeight' => 'all',
            'animated' => false
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
        $this->assertStringContainsString($pageData['slogan'], $response->getContent());
        $this->assertStringContainsString($pageData['complexName'], $response->getContent());
        $this->assertStringNotContainsString('first-screen__lead-container', $response->getContent());
    }

    public function testComponentWithNewsRowReturnValidResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $slider = [];
        for ($i = 0; $i <= 1; $i++) {
            $slide = $uploaded->image('slide' . $i . '.jpg', 1920, 1080);
            $slide = new TestFile($slide);
            $slide->setStorage($this->storage);
            $slide = $slide->load();
            $slider[] = $slide->id;
        }
        unset($slide);

        /**
         * @var Category $category
         * @var Element $newsElement
         */
        $category = Category::factory()->createOne(['active' => true]);
        $newsElement = Element::factory()->createOne(['category_id' => $category->getKey(), 'active' => true]);
        $buttonText = $faker->text;
        $buttonLink = $faker->url;

        $icon = new TestFile($uploaded->image('icon.svg'));
        $icon->setStorage($this->storage);

        $pageData = [
            'content' => [
                'slogan' => $faker->company,
                'complexName' => $faker->company,
                'alias' => 'home',
                'action' => [
                    'id' => $newsElement->getKey(),
                    'pageId' => $faker->randomNumber(),
                    'buttonText' => $buttonText,
                    'buttonLink' => $buttonLink,
                    'icon' => $icon->load()->getKey()
                ],
                'advantages' => [
                    ['title' => $faker->word, 'sort' => 500]
                ],
                'slider' => $slider,
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['slogan'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['complexName'], $response->getContent());
        $this->assertStringContainsString('<picture', $response->getContent());
        $this->assertStringContainsString('first-screen__lead-container', $response->getContent());
        $this->assertStringContainsString(e($newsElement->title), $response->getContent());
        $this->assertStringContainsString(e($buttonText), $response->getContent());
        $this->assertStringContainsString($buttonLink, $response->getContent());
    }

    public function testComponentWithVideoReturnValidResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'slogan' => $faker->company,
                'complexName' => $faker->company,
                'alias' => 'home',
                'action' => [],
                'advantages' => [
                    ['title' => $faker->word, 'sort' => 500]
                ],
                'slider' => [],
                'video' => $faker->randomElement([
                    static fn() => 'https://youtube.com/' . Str::random(rand(4, 10)),
                    static fn() => 'https://vimeo.com/' . rand(1000, 8000)
                ])()
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $video = \Kelnik\Core\Services\Video\Factory::make($pageData['content']['video']);
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['slogan'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['complexName'], $response->getContent());
        $this->assertStringContainsString($video->getThumb(), $response->getContent());
        $this->assertStringNotContainsString('<picture', $response->getContent());
    }

    public function testComponentWithEstateReturnValidResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);
        $this->seed(StatSeeder::class);

        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $slider = [];

        for ($i = 0; $i <= 1; $i++) {
            $slide = $uploaded->image('slide' . $i . '.jpg', 1920, 1080);
            $slide = new TestFile($slide);
            $slide->setStorage($this->storage);
            $slide = $slide->load();
            $slider[] = $slide->id;
        }
        unset($slide);

        $estateTypeTitle = $faker->sentence(1);
        $estateTypeUrl = $faker->url;

        $pageData = [
            'content' => [
                'slogan' => $faker->company,
                'complexName' => $faker->company,
                'alias' => 'home',
                'action' => [],
                'advantages' => [
                    ['title' => $faker->word, 'sort' => 500]
                ],
                'slider' => $slider,
                'estate' => [
                    'types' => [
                        [
                            'id' => StatSeeder::PREMISES_TYPE_1ROOM,
                            'active' => true,
                            'title' => $estateTypeTitle,
                            'url' => $estateTypeUrl
                        ]
                    ]
                ]
            ]
        ];

        $pageComponent->data->setValue(new Collection($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($estateTypeTitle, $response->getContent());
        $this->assertStringContainsString($estateTypeUrl, $response->getContent());
    }
}
