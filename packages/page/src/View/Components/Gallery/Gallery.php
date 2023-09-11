<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Gallery;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;

final class Gallery extends KelnikPageComponent implements HasContentAlias
{
    private CoreService $coreService;
    private PageService $pageService;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->pageService = resolve(PageService::class);
    }

    public static function getModuleName(): string
    {
        return PageServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-page::admin.components.gallery.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-page-gallery';
    }

    public function getContentAlias(): ?string
    {
        return Arr::get($this->getComponentData()->getValue()?->get('content'), 'alias');
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    protected function getTemplateData(): iterable
    {
        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $content = collect($this->getComponentData()->getValue()?->get('content') ?? []);
        $fileIds = (array) $content->pull('slider', []);
        $fileIds = array_filter($fileIds);

        if ($fileIds) {
            $hasImageModule = $this->coreService->hasModule('image');
            $code = 'gallery-' . $this->pageComponent->getKey();
            $slider = resolve(AttachmentRepository::class)
                ->getByPrimary($fileIds)
                ->map(static fn($slider) => [
                    'id' => $slider->getKey(),
                    'code' => $code,
                    'url' => $slider->url(),
                    'alt' => $slider->alt,
                    'description' => $slider->description,
                    'picture' => $hasImageModule
                        ? Picture::init(new ImageFile($slider))
                            ->setLazyLoad(true)
                            ->setBreakpoints([1440 => 1422, 1280 => 1280, 960 => 1208, 670 => 906, 320 => 632])
                            ->setImageAttribute('alt', $slider->alt ?? '')
                            ->render()
                        : null
                ]);
            $content->put('slider', $slider);
        }

        Cache::tags($this->pageService->getPageComponentCacheTag($this->pageComponent->id))
            ->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    public function render(): View|Closure|string
    {
        $data = $this->getTemplateData();
        $slider = $data->pull('slider');

        return view('kelnik-page::components.gallery', $data)->with('slider', $slider);
    }
}
