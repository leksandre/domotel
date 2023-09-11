<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\About;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;
use Kelnik\Core\View\Components\Contracts\HasMargin;
use Kelnik\Core\View\Components\Traits\Margin;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;

final class About extends KelnikPageComponent implements HasContentAlias, HasMargin
{
    use Margin;

    private readonly PageService $pageService;

    public function __construct()
    {
        $this->pageService = resolve(PageService::class);
    }

    public static function getModuleName(): string
    {
        return PageServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-page::admin.components.about.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-page-about';
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
        $content->put('textOnRight', (int)$content->get('textOnRight', 0) > 0);
        $content->put('margin', $this->getComponentData()->getValue()?->get('margin') ?? []);

        Cache::tags($this->pageService->getPageComponentCacheTag($this->pageComponent->id))
            ->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    public function render(): View|Closure|string
    {
        $data = $this->getTemplateData();
        $slider = $data->pull('slider');

        return view('kelnik-page::components.about', $data)->with('slider', $slider);
    }
}
