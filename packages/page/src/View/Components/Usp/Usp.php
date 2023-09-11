<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Usp;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\View\Components\Contracts\HasMargin;
use Kelnik\Core\View\Components\Traits\Margin;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Orchid\Attachment\Models\Attachment;

final class Usp extends KelnikPageComponent implements HasContentAlias, HasMargin
{
    use Margin;

    private readonly CoreService $coreService;
    private readonly PageService $pageService;

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
        return trans('kelnik-page::admin.components.usp.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-page-usp';
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
        $content->put('margin', $this->getComponentData()->getValue()?->get('margin') ?? []);
        $content->put('textOnLeft', (int)$content->get('textOnLeft', 0) > 0);
        $multiSlider = $content->get('multiSlider');

        $fileIds = (array) $content->pull('slider', []);
        $fileIds = array_filter($fileIds);

        $icon = $content->pull('icon');
        $icon = $icon ? (int)current($icon) : 0;
        if ($icon) {
            $fileIds[] = $icon;
        }

        if ($fileIds) {
            $images = resolve(AttachmentRepository::class)->getByPrimary($fileIds);
            $images = $images->filter(static function ($el) use (&$icon) {
                if (is_int($icon) && $icon === $el->id) {
                    $icon = $el;
                    return false;
                }

                return $el;
            });

            if ($icon instanceof Attachment) {
                $content->put('iconPath', $icon->url());
//                $storage = Storage::disk($icon->disk);
//                if ($storage->exists($icon->physicalPath())) {
//                    $content->put('iconBody', $storage->get($icon->physicalPath()));
//                }
            }

            $code = 'usp-' . $this->pageComponent->getKey();
            $slider = $images->map(fn($slider) => [
                    'id' => $slider->getKey(),
                    'code' => $code,
                    'url' => $slider->url(),
                    'alt' => $slider->alt,
                    'description' => $slider->description,
                    'picture' => $this->coreService->hasModule('image')
                        ? Picture::init(new ImageFile($slider))
                            ->setLazyLoad(true)
                            ->setBreakpoints(
                                $multiSlider
                                ? [1440 => 864, 1280 => 778, 960 => 864, 670 => 666, 320 => 666]
                                : [1440 => 800, 1280 => 720, 960 => 1066, 670 => 800, 320 => 632]
                            )
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

        return view('kelnik-page::components.usp.template', $data)->with('slider', $slider);
    }
}
