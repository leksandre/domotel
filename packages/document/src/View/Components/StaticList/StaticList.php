<?php

declare(strict_types=1);

namespace Kelnik\Document\View\Components\StaticList;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Document\Models\Category;
use Kelnik\Document\Providers\DocumentServiceProvider;
use Kelnik\Document\Repositories\Contracts\CategoryRepository;
use Kelnik\Document\Services\Contracts\DocumentService;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;

final class StaticList extends KelnikPageComponent implements HasContentAlias
{
    public static function getModuleName(): string
    {
        return DocumentServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-document::admin.components.staticList.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-document-staticList';
    }

    public function getContentAlias(): ?string
    {
        return Arr::get($this->getComponentData()->getValue()?->get('content'), 'alias');
    }

    protected function getTemplateData(): array
    {
        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $content = $this->getComponentData()->getValue()?->get('content');
        $content['group'] = (int)($content['group'] ?? 0);
        $content['template'] = self::getTemplates()->first()?->name;
        $content['list'] = resolve(CategoryRepository::class)->getActiveWithElements($content['group']);

        if ($content['list']->isNotEmpty()) {
            $content['list'] = $content['list']->filter(
                static fn(Category $category)
                => $category->relationLoaded('elements') && $category->elements->isNotEmpty()
            );
        }

        /** @var DocumentService $docService */
        $docService = resolve(DocumentService::class);

        $content['list'] = $docService->prepareList($content['list']);

        $cacheTags = [
            resolve(PageService::class)->getPageComponentCacheTag($this->pageComponent->id),
            $docService->getCacheTag()
        ];

        if ($content['group']) {
            $cacheTags[] = $docService->getGroupCacheTag($content['group']);
        }

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
        $template = $data['template'] ?? self::getTemplates()->first()->name;
        unset($data['template']);

        return isset($data['list']) && $data['list']->isNotEmpty() && $template
                ? view($template, $data)
                : null;
    }

    public static function getTemplates(): Collection
    {
        return new Collection([
            new StaticListTemplate(
                'kelnik-document::components.staticList.accordion',
                trans('kelnik-document::admin.components.staticList.templates.accordion')
            )
        ]);
    }
}
