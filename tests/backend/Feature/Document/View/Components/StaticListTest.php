<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Document\View\Components;

use Exception;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Kelnik\Document\Models\Category;
use Kelnik\Document\Models\Element;
use Kelnik\Document\Repositories\Contracts\CategoryRepository;
use Kelnik\Document\Services\Contracts\DocumentService;
use Kelnik\Document\View\Components\StaticList\StaticList;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Kelnik\Tests\TestFile;
use Mockery;

final class StaticListTest extends TestCase
{
    use RefreshDatabase;
    use SiteTrait;

    private Generator $faker;
    private Filesystem $storage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initSite();
        $this->storage = Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
        $this->faker = Factory::create(config('app.faker_locale'));
    }

    private function getDefaultPageData(): array
    {
        return [
            'content' => [
                'title' => $this->faker->company,
                'alias' => $this->faker->slug(),
            ]
        ];
    }

    private function createDocuments(): Collection
    {
        /** @var Category $category */
        $category = Category::factory()->createOne(['active' => true]);
        $res = new Collection();
        $maxCnt = rand(5, 15);
        $uploaded = UploadedFile::fake();
        $fileExt = ['xlsx','xls', 'docx', 'doc', 'pdf', 'jpg', 'png', 'zip', 'rar'];

        $hasActive = false;
        $hasInActive = false;

        for ($i = 0; $i <= $maxCnt; $i++) {
            $doc = Element::factory()->makeOne([
                'category_id' => $category->getKey()
            ]);

            if ($doc->active) {
                $hasActive = true;
            } else {
                $hasInActive = true;
            }

            $attachment = $uploaded->createWithContent(
                $this->faker->unique()->sentence(3) . '.' . $this->faker->randomElement($fileExt),
                $this->faker->unique()->randomAscii()
            );
            $attachment = new TestFile($attachment);
            $attachment->setStorage($this->storage);
            $attachment = $attachment->load();
            $doc->attachment_id = $attachment->id;
            $doc->save();

            $res->add($doc);
        }

        if (!$hasActive) {
            $doc = $res->random(1)->first();
            $doc->active = true;
            $doc->save();
        }

        if (!$hasInActive) {
            $doc = $res->random(1)->first();
            $doc->active = false;
            $doc->save();
        }

        return $res;
    }

    private function createPage(): Page
    {
        return Page::factory()->createOne([
            'site_id' => $this->site->getKey(),
            'active' => true
        ]);
    }

    /** @throws Exception */
    private function addComponentToPage(Model $page, string $componentNamespace): PageComponent
    {
        $pageComponent = PageComponent::factory()->makeOne([
            'active' => true,
            'component' => $componentNamespace
        ]);

        if (!$page->components()->save($pageComponent)) {
            throw new Exception('Can\'t associate component to page');
        }

        return $page->components()?->first() ?? $pageComponent;
    }

    public function testComponentExists()
    {
        /** @var BladeComponentRepository $componentRepository */
        $componentRepository = resolve(BladeComponentRepository::class);
        $components = $componentRepository->getAdminList()->keys()->toArray();

        $this->assertContains(StaticList::initDataProvider()->getComponentCode(), $components);
    }

    public function testComponentAddedToPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, StaticList::class);

        $this->assertDatabaseHas(
            $pageComponent->getTable(),
            [
                'page_id' => $page->getKey(),
                'component' => $pageComponent->component
            ]
        );
    }

    public function testComponentReturnValidContentAlias()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, StaticList::class);

        $pageData = $this->getDefaultPageData();

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $viewComponent = resolve(PageService::class)->initViewComponent($page, $pageComponent);

        $this->assertTrue($viewComponent->getContentAlias() === $pageData['content']['alias']);
    }

    public function testComponentReturnValidResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, StaticList::class);
        $docs = $this->createDocuments();

        $pageData = $this->getDefaultPageData();

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $firstActive = $docs->first(static fn(Element $el) => $el->active);
        $firstInActive = $docs->first(static fn(Element $el) => !$el->active);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($firstActive->title, $response->getContent());
        $this->assertStringNotContainsString($firstInActive->title, $response->getContent());
        $this->assertStringContainsString($firstActive->attachment->url(), $response->getContent());
    }

    public function testComponentReturnEmptyResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, StaticList::class);

        $pageData = [
            'content' => [
                'title' => $this->faker->company,
                'alias' => $this->faker->slug(),
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringNotContainsString($pageData['content']['title'], $response->getContent());
    }

    public function testComponentUseCacheOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, StaticList::class);
        $docs = $this->createDocuments();

        $pageData = $this->getDefaultPageData();

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $pageData = $pageData['content'];
        $pageData['list'] = resolve(CategoryRepository::class)->getActiveWithElements();
        $pageData['list'] = resolve(DocumentService::class)->prepareList($pageData['list']);

        $cacheId = resolve(PageService::class)
            ->getPageComponentCacheTag($page->getKey() . '_' . $pageComponent->getKey());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['title'], $response->getContent());
    }

    public function testComponentReturnEmptyResultOnPageThenUseGroup()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, StaticList::class);
        $docs = $this->createDocuments();

        $pageData = $this->getDefaultPageData();
        $pageData['content']['group'] = rand(((int)$docs->first()?->category_id) + 1, 10);

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringNotContainsString($pageData['content']['title'], $response->getContent());
    }
}
