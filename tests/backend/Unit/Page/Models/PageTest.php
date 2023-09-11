<?php

declare(strict_types=1);

namespace Kelnik\Tests\Unit\Page\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kelnik\Page\Models\Page;
use Kelnik\Tests\TestCase;

final class PageTest extends TestCase
{
    use RefreshDatabase;

    private Page $page;

    public function setUp(): void
    {
        parent::setUp();

        $this->page = Page::factory()->createOne();
    }

    public function testUnique()
    {
        $err = false;
        try {
            Page::factory()->createOne([
                'site_id' => $this->page->site_id,
                'parent_id' => $this->page->parent_id ?? 0,
                'slug' => $this->page->slug
            ]);
        } catch (\Exception $e) {
            $err = $e;
        }

        $this->assertTrue($err instanceof QueryException);
    }

    public function testGetUrl()
    {
        $this->assertTrue($this->page->getUrl() === '/' . $this->page->slug);
    }

    public function testNotHaveChildren()
    {
        $children = $this->page->children;

        $this->assertTrue($children instanceof Collection && !$children->count());
    }

    public function testNotHaveParent()
    {
        $this->assertTrue(!$this->page->parent_id && !$this->page->parent->exists);
    }

    public function testCheckIsHome()
    {
        $homePage = Page::factory()->createOne([
            'slug' => null,
            'title' => 'Main'
        ]);

        $this->assertTrue($homePage->isHome() && $homePage->slug === null && !$homePage->hasParent());
    }
}
