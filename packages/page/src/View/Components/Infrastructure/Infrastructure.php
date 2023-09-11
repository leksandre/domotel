<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Infrastructure;

use Closure;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Orchid\Attachment\Models\Attachment;

final class Infrastructure extends KelnikPageComponent implements HasContentAlias
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
        return trans('kelnik-page::admin.components.infrastructure.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-page-infrastructure';
    }

    public function getContentAlias(): ?string
    {
        return Arr::get($this->getComponentData()->getValue()?->get('content'), 'alias');
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    /** @throws FileNotFoundException */
    protected function getTemplateData(): iterable
    {
        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $content = collect($this->getComponentData()->getValue()?->get('content') ?? []);

        if ($content->get('text') === '<p><br></p>') {
            $content->forget('text');
        }

        $legend = $content->pull('legend') ?? [];
        $fileIds = array_column($legend, 'icon');
        $fileIds = array_filter($fileIds);

        $plan = $content->pull('plan');
        $plan = $plan ? (int)$plan : 0;
        if ($plan) {
            $fileIds[] = $plan;
        }

        if ($fileIds) {
            $images = resolve(AttachmentRepository::class)->getByPrimary($fileIds);
            $images = $images->filter(static function ($el) use (&$plan) {
                if (is_int($plan) && $plan === $el->id) {
                    $plan = $el;
                    return false;
                }

                return $el;
            });

            if ($plan instanceof Attachment) {
                $storage = Storage::disk($plan->disk);
                if ($storage->exists($plan->physicalPath())) {
                    $content->put(
                        'plan',
                        strtolower($plan->extension) === 'svg'
                            ? $storage->get($plan->physicalPath())
                            : ($this->coreService->hasModule('image')
                                ? Picture::init(new ImageFile($plan))
                                    ->setLazyLoad(true)
                                    ->setBreakpoints([1280 => 800, 960 => 1066, 670 => 800, 320 => 558])
                                    ->setImageAttribute('alt', $plan->alt ?? '')
                                    ->render()
                                : $plan)
                    );
                }
                unset($storage, $plan);
            }

            $legend = array_map(
                static function ($el) use ($images) {
                    $el['iconPath'] = $images->first(static fn($img) => $img->id === $el['icon'])?->url();

                    /*if (!$icon || strtolower($icon->extension) !== 'svg') {
                        $el['icon'] = $icon ?: null;
                        return $el;
                    }
                    $storage = Storage::disk($icon->disk);
                    $el['icon'] = $storage->exists($icon->physicalPath())
                        ? $storage->get($icon->physicalPath())
                        : null;*/

                    return $el;
                },
                $legend
            );
            unset($images);
            $content->put('legend', $legend);
        }

        Cache::tags($this->pageService->getPageComponentCacheTag($this->pageComponent->id))
            ->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    public function render(): View|Closure|string
    {
        return view('kelnik-page::components.infrastructure', $this->getTemplateData());
    }
}
