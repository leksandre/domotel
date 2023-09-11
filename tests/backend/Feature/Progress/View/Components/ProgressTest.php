<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Progress\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Progress\Models\Album;
use Kelnik\Progress\Models\Camera;
use Kelnik\Progress\View\Components\Progress\Progress;
use Kelnik\Tests\Feature\Page\View\Components\AbstractTestComponent;
use Kelnik\Tests\Feature\Progress\DynContent;
use Mockery;

final class ProgressTest extends AbstractTestComponent
{
    use DynContent;
    use RefreshDatabase;

    protected KelnikPageComponent|string $componentNamespace = Progress::class;

    private function createPageWithComponent(): array
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->sentence,
                'alias' => $faker->slug,
                'text' => $faker->realText,
                'deadlines' => [],
                'buttonText' => $faker->unique()->sentence(2)
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        return [
            'page' => $page,
            'component' => $pageComponent
        ];
    }

    public function testComponentReturnValidContentAlias()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->sentence,
                'alias' => $faker->slug,
                'text' => $faker->realText,
                'deadlines' => [],
                'buttonText' => ''
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $viewComponent = resolve(PageService::class)->initViewComponent($page, $pageComponent);

        $this->assertTrue($viewComponent->getContentAlias() === $pageData['content']['alias']);
    }

    /** @depends testComponentAddedToPage */
    public function testComponentReturnValidResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->sentence,
                'alias' => $faker->slug,
                'text' => $faker->realText,
                'deadlines' => [],
                'buttonText' => $faker->unique()->sentence(2)
            ]
        ];

        $maxDeadlines = rand(3, 7);
        for ($i = 0; $i <= $maxDeadlines; $i++) {
            $pageData['content']['deadlines'][] = [
                'title' => $faker->unique()->company(),
                'text' => $faker->unique()->sentence(10, true)
            ];
        }
        $randomDeadlines = array_rand($pageData['content']['deadlines']);
        $randomDeadlines = $pageData['content']['deadlines'][$randomDeadlines];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['alias'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['text'], $response->getContent());
        $this->assertStringContainsString($randomDeadlines['title'], $response->getContent());
        $this->assertStringContainsString($randomDeadlines['text'], $response->getContent());
    }

    public function testComponentShouldShowCamerasButton()
    {
        // Dynamic content
        $camera = Camera::factory()->createOne(['active' => true]);

        // Page component
        ['page' => $page, 'component' => $pageComponent] = $this->createPageWithComponent();

        // Asserts
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString(
            'data-ajax="' . route('kelnik.progress.cameras', [], false) . '"',
            $response->getContent()
        );
    }

    public function testComponentShouldNotShowCamerasButton()
    {
        // Dynamic content
        $camera = Camera::factory()->createOne(['active' => false]);

        // Page component
        ['page' => $page, 'component' => $pageComponent] = $this->createPageWithComponent();

        // Asserts
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringNotContainsString(
            'data-ajax="' . route('kelnik.progress.cameras', [], false) . '"',
            $response->getContent()
        );
    }

    public function testComponentShouldNotShowInactiveAlbum()
    {
        // Dynamic content
        $album = Album::factory()->createOne(['active' => false]);
        $this->addImagesToAlbum($album);

        // Page component
        ['page' => $page, 'component' => $pageComponent] = $this->createPageWithComponent();

        // Asserts
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringNotContainsString(
            'data-ajax="' . route('kelnik.progress.albums', [], false) . '"',
            $response->getContent()
        );
        $this->assertStringNotContainsString($album->title, $response->getContent());
        $this->assertStringNotContainsString('<span class="progress-card__count-text">', $response->getContent());
    }

    public function testComponentShouldNotShowActiveAlbumWithoutImages()
    {
        // Dynamic content
        $album = Album::factory()->createOne(['active' => true]);

        // Page component
        ['page' => $page, 'component' => $pageComponent] = $this->createPageWithComponent();

        // Asserts
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringNotContainsString(
            'data-ajax="' . route('kelnik.progress.albums', [], false) . '"',
            $response->getContent()
        );
        $this->assertStringNotContainsString($album->title, $response->getContent());
        $this->assertStringNotContainsString('<span class="progress-card__count-text">', $response->getContent());
    }

    public function testComponentShouldShowActiveAlbumWithImagesAndVideos()
    {
        // Dynamic content
        $album = Album::factory()->hasVideos(3)->createOne(['active' => true]);
        $this->addImagesToAlbum($album);
        $imagesCnt = $album->images->count();
        $videosCnt = $album->videos->count();

        // Page component
        ['page' => $page, 'component' => $pageComponent] = $this->createPageWithComponent();

        // Asserts
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString(
            'data-ajax="' . route('kelnik.progress.albums', [], false) . '"',
            $response->getContent()
        );
        $this->assertStringContainsString($album->title, $response->getContent());
        $this->assertStringContainsString(
            '<span class="progress-card__count-text">' . $videosCnt . '</span>',
            $response->getContent()
        );
        $this->assertStringContainsString(
            '<span class="progress-card__count-text">' . $imagesCnt . '</span>',
            $response->getContent()
        );
    }

    public function testComponentShouldShowActiveAlbumWithoutImagesButWithVideos()
    {
        // Dynamic content
        $album = Album::factory()->hasVideos(3)->createOne(['active' => true]);
        $imagesCnt = $album->images->count();
        $videosCnt = $album->videos->count();
        $cover = \Kelnik\Core\Services\Video\Factory::make($album->videos->first()->url)?->getThumb();

        // Page component
        ['page' => $page, 'component' => $pageComponent] = $this->createPageWithComponent();

        // Asserts
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString(
            'data-ajax="' . route('kelnik.progress.albums', [], false) . '"',
            $response->getContent()
        );
        $this->assertStringContainsString($album->title, $response->getContent());
        $this->assertStringContainsString(
            '<span class="progress-card__count-text">' . $videosCnt . '</span>',
            $response->getContent()
        );
        $this->assertStringContainsString($cover, $response->getContent());
    }

    public function testComponentUseCacheOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'title' => $faker->sentence,
            'alias' => $faker->slug,
            'text' => $faker->realText,
            'deadlines' => [],
            'buttonText' => $faker->unique()->sentence(2)
        ];

        $maxDeadlines = rand(3, 7);
        for ($i = 0; $i <= $maxDeadlines; $i++) {
            $pageData['deadlines'][] = [
                'title' => $faker->unique()->company(),
                'text' => $faker->unique()->sentence(10, true)
            ];
        }
        $randomDeadline = array_rand($pageData['deadlines']);
        $randomDeadline = $pageData['deadlines'][$randomDeadline];

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
        $this->assertStringContainsString($pageData['alias'], $response->getContent());
        $this->assertStringContainsString($pageData['text'], $response->getContent());
        $this->assertStringContainsString($randomDeadline['title'], $response->getContent());
        $this->assertStringContainsString($randomDeadline['text'], $response->getContent());
    }

    public function testComponentShouldNotShowAlbumOrVideoThenUseGroup()
    {
        // Dynamic content
        $album = Album::factory()->hasVideos(3)->createOne(['active' => true]);
        $this->addImagesToAlbum($album);

        // Page component
        ['page' => $page, 'component' => $pageComponent] = $this->createPageWithComponent();

        $content = $pageComponent->data->get('content');
        $content['group'] = rand(1, 10);

        $pageComponent->data->put('content', $content);
        $pageComponent->save();

        // Asserts
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringNotContainsString(
            'data-ajax="' . route('kelnik.progress.albums', [], false) . '"',
            $response->getContent()
        );
        $this->assertStringNotContainsString($album->title, $response->getContent());
    }
}
