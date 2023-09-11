<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Page\Http\Controllers;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Repositories\Contracts\PageRepository;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Kelnik\Tests\TestFile;

final class PageControllerTest extends TestCase
{
    use RefreshDatabase;
    use SiteTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initSite();
    }

    private function makePage(): Page
    {
        return Page::factory()->makeOne([
            'active' => true,
            'site_id' => $this->site->getKey()
        ]);
    }

    public function testPageHasDefaultMetaTags(): void
    {
        /** @var Page $page */
        $page = Page::factory()->createOne([
            'active' => true,
            'site_id' => $this->site->getKey()
        ]);
        $res = $this->get($page->getUrl());

        $res->assertOk();
        $this->assertStringContainsString('<title>' . $page->title . '</title>', $res->getContent());
        $this->assertStringContainsString(
            '<meta property="og:title" content="' . $page->title . '">',
            $res->getContent()
        );
    }

    public function testPageHasCustomMetaTags(): void
    {
        $faker = Factory::create();

        $title = $faker->unique()->sentence();
        $description = $faker->unique()->sentence();
        $keywords = $faker->unique()->sentence();

        $page = $this->makePage();
        $page->meta->setTitle($title);
        $page->meta->setDescription($description);
        $page->meta->setKeywords($keywords);

        resolve(PageRepository::class)->save($page);

        $res = $this->get($page->getUrl());

        $content = $res->getContent();

        $res->assertOk();
        $this->assertStringContainsString('<title>' . $title . '</title>', $content);
        $this->assertStringNotContainsString('<title>' . $page->title . '</title>', $content);
        $this->assertStringContainsString('<meta property="og:title" content="' . $title . '">', $content);
        $this->assertStringContainsString(
            '<meta property="og:description" content="' . $description . '">',
            $content
        );
        $this->assertStringContainsString('<meta name="description" content="' . $description . '">', $content);
        $this->assertStringContainsString('<meta name="keywords" content="' . $keywords . '">', $content);
    }

    public function testPageHasImageMetaTag(): void
    {
        $page = $this->makePage();

        $uploaded = UploadedFile::fake();
        $img = $uploaded->image('img.jpg', 300, 400);
        $img = new TestFile($img);
        $img->setStorage(Storage::fake('public'));
        $img = $img->load();

        $page->meta->setImage($img);

        resolve(PageRepository::class)->save($page);

        $res = $this->get($page->getUrl());

        $content = $res->getContent();

        $res->assertOk();
        $this->assertStringContainsString('<meta property="og:image" content="' . $img->url() . '">', $content);
    }
}
