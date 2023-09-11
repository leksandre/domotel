<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\FlatBlock\View\Components;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\FBlock\Models\Button;
use Kelnik\FBlock\Models\FlatBlock;
use Kelnik\FBlock\Repositories\Contracts\BlockRepository;
use Kelnik\FBlock\Services\Contracts\BlockService;
use Kelnik\FBlock\View\Components\BlockList\BlockList;
use Kelnik\Form\Models\Form;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Tests\Feature\PageComponentTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Kelnik\Tests\TestFile;
use Mockery;

final class BlockListTest extends TestCase
{
    use RefreshDatabase;
    use PageComponentTrait;
    use SiteTrait;

    private const ITEMS_MIN = 2;
    private const ITEMS_MAX = 10;

    private Filesystem $storage;
    private PageService $pageService;
    private SettingsService $settingsService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
        $this->pageService = resolve(PageService::class);
        $this->settingsService = resolve(SettingsService::class);
        $this->initSite();
    }

    private function createBlocks(): Collection
    {
        $res = FlatBlock::factory()->count(rand(self::ITEMS_MIN, self::ITEMS_MAX))->create();

        $hasActive = $res->first(static fn(FlatBlock $el) => $el->active === true);

        if (!$hasActive) {
            $el = $res->first();
            $el->active = true;
            resolve(BlockRepository::class)->save($el);
        }

        return $res;
    }

    private function createForm(): Form
    {
        return Form::factory()->createOne(['active' => true]);
    }

    public function testComponentExists()
    {
        /** @var BladeComponentRepository $componentRepository */
        $componentRepository = resolve(BladeComponentRepository::class);
        $components = $componentRepository->getAdminList()->keys()->toArray();

        $this->assertContains(BlockList::initDataProvider()->getComponentCode(), $components);
    }

    public function testComponentAddedToPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, BlockList::class);

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
        $pageComponent = $this->addComponentToPage($page, BlockList::class);
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->sentence(3),
                'alias' => $faker->slug,
                'text' => $faker->unique()->realText()
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $viewComponent = resolve(PageService::class)->initViewComponent($page, $pageComponent);

        $this->assertTrue($viewComponent->getContentAlias() === $pageData['content']['alias']);
    }

    public function testSectionIsVisibleWithoutBlocks()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, BlockList::class);

        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->sentence(3),
                'alias' => 'flats-1',
                'text' => $faker->unique()->realText()
            ],
            'template' => BlockList::getTemplates()->last()->name
        ];

        $uploaded = UploadedFile::fake();
        $flatPlan = $uploaded->image('flat-plan.jpg', 800, 600);
        $flatPlan = new TestFile($flatPlan);
        $flatPlan->setStorage($this->storage);
        $flatPlan = $flatPlan->load();

        $pageData['content']['image'] = $flatPlan->getKey();

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['alias'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['text'], $response->getContent());
        $this->assertStringContainsString($flatPlan->name, $response->getContent());
    }

    public function testShouldShowBlocks()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, BlockList::class);

        $faker = Factory::create(config('app.faker_locale'));
        $this->createBlocks();
        $repository = resolve(BlockRepository::class);
        $activeBlocks = $repository->getActiveList();

        $form = resolve(CoreService::class)->hasModule('form')
            ? $this->createForm()
            : null;

        $formDisabled = false;

        if ($form) {
            $done = false;
            $activeBlocks = $activeBlocks->each(
                static function (FlatBlock $block) use ($form, $repository, &$done, $faker, &$formDisabled) {
                    if ($block->active && !$done) {
                        $formId = $form->getKey();
                        if ($faker->boolean) {
                            $formId = 0;
                            $formDisabled = true;
                        }
                        $block->button = new Button($formId, $form->title);
                        $repository->save($block);
                        $done = true;
                    }
                }
            );
        }

        $uploaded = UploadedFile::fake();
        $flatPlan = $uploaded->image('flat-plan.jpg', 800, 600);
        $flatPlan = new TestFile($flatPlan);
        $flatPlan->setStorage($this->storage);
        $flatPlan = $flatPlan->load();

        $block = $activeBlocks->first();
        $repository->save($block, [$flatPlan->getKey()]);

        $pageData = [
            'content' => [
                'title' => $faker->sentence(3),
                'alias' => 'flats-3',
                'text' => $faker->unique()->realText(),
            ],
            'blocks' => $activeBlocks,
            'colors' => [],
            'template' => BlockList::getTemplates()->last()->name
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $cacheId = $this->pageService->getPageComponentCacheTag($page->id . '_' . $pageComponent->id);

        $cacheTags = [
            $this->pageService->getPageComponentCacheTag($pageComponent->id),
            resolve(BlockService::class)->getCacheTag(),
            $this->settingsService->getCacheTag(
                CoreServiceProvider::MODULE_NAME,
                $this->settingsService::PARAM_COLORS
            )
        ];

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['alias'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['text'], $response->getContent());
        $this->assertTrue(!$form || $formDisabled || str_contains($response->getContent(), $form->title));
        $this->assertTrue(Cache::tags($cacheTags)->has($cacheId));
    }

    public function testShowBlocksUsingCache()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, BlockList::class);

        $faker = Factory::create(config('app.faker_locale'));

        $blocks = $this->createBlocks();

        $pageData = [
            'content' => [
                'title' => $faker->sentence(3),
                'alias' => 'flats-3',
                'text' => $faker->unique()->realText(),
            ],
            'blocks' => $blocks,
            'template' => BlockList::getTemplates()->last()->name
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $pageData['content']['blocks'] = $pageData['blocks'];
        $pageData['content']['template'] = $pageData['template'];
        $pageData = $pageData['content'];

        $cacheId = resolve(PageService::class)
            ->getPageComponentCacheTag($page->getKey() . '_' . $pageComponent->getKey());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['alias'], $response->getContent());
        $this->assertStringContainsString($pageData['title'], $response->getContent());
        $this->assertStringContainsString($pageData['text'], $response->getContent());
    }
}
