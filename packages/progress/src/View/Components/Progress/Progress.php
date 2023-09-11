<?php

declare(strict_types=1);

namespace Kelnik\Progress\View\Components\Progress;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Progress\Providers\ProgressServiceProvider;
use Kelnik\Progress\Services\Contracts\ProgressService;

final class Progress extends KelnikPageComponent implements HasContentAlias
{
    private ProgressService $progressService;
    private PageService $pageService;

    public function __construct()
    {
        $this->progressService = resolve(ProgressService::class);
        $this->pageService = resolve(PageService::class);
    }

    public static function getModuleName(): string
    {
        return ProgressServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-progress::admin.components.progress.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-progress-progress';
    }

    public function getContentAlias(): ?string
    {
        return Arr::get($this->getComponentData()->getValue()?->get('content'), 'alias');
    }

    protected function getTemplateData(): iterable
    {
        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $content = $this->getComponentData()->getValue()?->get('content');
        $content['buttonText'] = $content['buttonText'] ?: trans('kelnik-progress::front.buttonText');

        $cacheTags = [
            $this->pageService->getPageComponentCacheTag($this->pageComponent->id),
            $this->progressService->getAlbumListCacheTag(),
            $this->progressService->getCameraListCacheTag()
        ];

        $content['group'] = (int)($content['group'] ?? 0);

        if ($content['group']) {
            $cacheTags[] = $this->progressService->getGroupCacheTag($content['group']);
        }

        $contentObjects = [
            'albums' => [
                'data' => 'getAlbums',
                'callback' => [$this->progressService, 'getAlbumCacheTag']
            ],
            'cameras' => [
                'data' => 'getCameras',
                'callback' => [$this->progressService, 'getCameraCacheTag']
            ]
        ];

        foreach ($contentObjects as $objName => $params) {
            $content[$objName] = $this->progressService->{$params['data']}(group: $content['group']);

            if ($content[$objName]->isEmpty()) {
                continue;
            }

            $content[$objName]->each(static function (Model $el) use (&$cacheTags, $params) {
                $cacheTags[] = call_user_func($params['callback'], $el->getKey());
            });
        }
        unset($contentObjects);

        Cache::tags($cacheTags)->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();

        $albums = Arr::pull($data, 'albums') ?? new Collection();
        $cameras = Arr::pull($data, 'cameras') ?? new Collection();

        return view('kelnik-progress::components.progress.section', $data)
            ->with('albums', $albums)
            ->with('cameras', $cameras);
    }
}
