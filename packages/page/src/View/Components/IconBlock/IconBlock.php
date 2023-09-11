<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\IconBlock;

use Closure;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Orchid\Attachment\Models\Attachment;

final class IconBlock extends KelnikPageComponent implements HasContentAlias
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
        return trans('kelnik-page::admin.components.iconBlock.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-page-icon-block';
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

        $lineLimit = (int)$content->get('lineLimit');

        if ($lineLimit < DataProvider::USP_MIN || $lineLimit > DataProvider::USP_MAX) {
            $lineLimit = DataProvider::USP_MIN;
        }

        $content->put('lineLimit', $lineLimit);

        $items = $content->pull('list') ?? [];
        $fileIds = array_filter(array_column($items, 'icon'));

        $images = $fileIds ? resolve(AttachmentRepository::class)->getByPrimary($fileIds) : new Collection();
        $items = array_map(
            static function ($el) use ($images) {
                $icon = $images->first(static fn($img) => $img->id === $el['icon']);
                $el['iconPath'] = $el['iconBody'] = null;

                if (!$icon instanceof Attachment) {
                    return $el;
                }

                $el['iconPath'] = $icon->url();

                if (strtolower($icon->extension) === 'svg') {
                    $storage = Storage::disk($icon->disk);
                    $el['iconBody'] = $storage->exists($icon->physicalPath())
                        ? $storage->get($icon->physicalPath())
                        : null;
                }

                return $el;
            },
            $items
        );
        unset($images);
        $content->put('list', $items);

        Cache::tags($this->pageService->getPageComponentCacheTag($this->pageComponent->id))
            ->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    public function render(): View|Closure|string
    {
        return view('kelnik-page::components.icon-block', $this->getTemplateData());
    }
}
