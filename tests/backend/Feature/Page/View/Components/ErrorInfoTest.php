<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Page\View\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Form\Models\Form;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Kelnik\Menu\Models\Menu;
use Kelnik\Page\Database\Seeders\PageErrorSeeder;
use Kelnik\Page\Models\Enums\Type;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Page\View\Components\ErrorInfo\ErrorInfo;
use Kelnik\Tests\TestFile;
use Mockery;
use Symfony\Component\HttpFoundation\Response;

final class ErrorInfoTest extends AbstractTestComponent
{
    use RefreshDatabase;

    protected KelnikPageComponent|string $componentNamespace = ErrorInfo::class;

    private function createMenu(): Menu
    {
        return Menu::factory()->hasItems(10)->createOne(['active' => true]);
    }

    private function createForm(): Form
    {
        return Form::factory()->createOne(['active' => true]);
    }

    protected function createPage(): Page
    {
        return Page::factory()->createOne([
            'site_id' => $this->site->getKey(),
            'type' => Type::Error,
            'active' => true
        ]);
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

    public function testComponentReturnValidResultOnPage()
    {
        (new PageErrorSeeder($this->site))->run();

        $uploaded = UploadedFile::fake();
        $img = $uploaded->image('bg-file.jpg', 1920, 1080);
        $img = new TestFile($img);
        $img->setStorage($this->storage);
        $img = $img->load();

        /**
         * @var Page $page
         * @var PageComponent $pageComponent
         */
        $page = Page::query()->where('type', Type::Error->value)->firstOrFail();
        $pageComponent = $page->components()->where('component', ErrorInfo::class)->firstOrFail();

        $pageData = $pageComponent->data->getValue()->get('content') ?? [];
        $pageData['background'] = $img->id;
        $pageComponent->data->setValue(new Collection(['content' => $pageData]));
        $pageComponent->saveQuietly();

        $code = Response::HTTP_NOT_FOUND;
        $title = $pageData['text'][$code]['title'] ?? trans(
            'kelnik-page::admin.components.errorInfo.state.' . $code . '.title'
        );
        $text = $pageData['text'][$code]['text'] ?? trans(
            'kelnik-page::admin.components.errorInfo.state.' . $code . '.text'
        );

        $response = $this->get('/' . Str::slug(Str::random()));

        $response->assertStatus($code);
        $this->assertStringContainsString($title, $response->getContent());
        $this->assertStringContainsString($text, $response->getContent());
        $this->assertStringContainsString($img->name, $response->getContent());
    }

    public function testComponentReturnValidResultOnPageUsingCache()
    {
        (new PageErrorSeeder($this->site))->run();

        $uploaded = UploadedFile::fake();
        $img = $uploaded->image('bg-file2.jpg', 1920, 1080);
        $img = new TestFile($img);
        $img->setStorage($this->storage);
        $img = $img->load();

        /**
         * @var Page $page
         * @var PageComponent $pageComponent
         */
        $page = Page::query()->where('type', Type::Error->value)->firstOrFail();
        $pageComponent = $page->components()->where('component', ErrorInfo::class)->firstOrFail();
        $code = Response::HTTP_NOT_FOUND;
        $hasImage = resolve(CoreService::class)->hasModule('image');

        if ($hasImage) {
            $img = Picture::init(new ImageFile($img))
                ->setLazyLoad(true)
                ->setBreakpoints([1280 => 1280, 960 => 1208, 670 => 906, 320 => 632])
                ->setImageAttribute('alt', $img->alt ?? '')
                ->render();
        }

        $pageData = new Collection([
            'code' => $code,
            'title' => trans('kelnik-page::admin.components.errorInfo.state.' . $code . '.title'),
            'text' => trans('kelnik-page::admin.components.errorInfo.state.' . $code . '.text'),
            'buttons' => [],
            'background' => $img
        ]);

        $cacheId = resolve(PageService::class)
            ->getPageComponentCacheTag($page->getKey() . '_' . $pageComponent->getKey()) . '_' . $code;

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get('/' . Str::slug(Str::random()));

        $response->assertStatus($code);
        $this->assertStringContainsString($pageData->get('title'), $response->getContent());
        $this->assertStringContainsString($pageData->get('text'), $response->getContent());
        $this->assertStringContainsString((is_string($img) ? $img : $img->name), $response->getContent());
    }
}
