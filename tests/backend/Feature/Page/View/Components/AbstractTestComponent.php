<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Page\View\Components;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;

abstract class AbstractTestComponent extends TestCase
{
    use SiteTrait;

    protected Filesystem $storage;
    protected KelnikPageComponent|string $componentNamespace = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
        $this->initSite();
    }

    protected function createPage(): Page
    {
        return Page::factory()->createOne([
            'site_id' => $this->site->getKey(),
            'active' => true
        ]);
    }

    /** @throws \Exception */
    protected function addComponentToPage(Model $page): PageComponent
    {
        $pageComponent = PageComponent::factory()->makeOne([
            'active' => true,
            'component' => $this->componentNamespace
        ]);

        if (!$page->components()->save($pageComponent)) {
            throw new \Exception('Can\'t associate component to page');
        }

        return $page->components()?->first() ?? $pageComponent;
    }

    public function testComponentExists()
    {
        /** @var BladeComponentRepository $componentRepository */
        $componentRepository = resolve(BladeComponentRepository::class);
        $components = $componentRepository->getAdminList()->keys()->toArray();

        $this->assertContains($this->componentNamespace::initDataProvider()->getComponentCode(), $components);
    }

    public function testComponentAddedToPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);
        $this->assertDatabaseHas(
            $pageComponent->getTable(),
            [
                'page_id' => $page->getKey(),
                'component' => $pageComponent->component
            ]
        );
    }
}
