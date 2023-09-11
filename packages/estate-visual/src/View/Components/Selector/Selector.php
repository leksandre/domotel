<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\View\Components\Selector;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Kelnik\EstateVisual\Repositories\Contracts\SelectorRepository;
use Kelnik\EstateVisual\Services\Contracts\SelectorService;
use Kelnik\EstateVisual\Services\Contracts\VisualService;
use Kelnik\EstateVisual\View\Components\Contracts\AbstractSelector;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Contracts\RouteProvider;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageDynamicComponent;
use Symfony\Component\HttpFoundation\Response;

final class Selector extends AbstractSelector implements KelnikPageDynamicComponent
{
    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    public static function initRouteProvider(Page $page, PageComponent $pageComponent): RouteProvider
    {
        return new \Kelnik\EstateVisual\View\Components\Selector\RouteProvider($page, $pageComponent);
    }

    public static function getTitle(): string
    {
        return trans('kelnik-estate-visual::admin.components.selector.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-estate-visual-selector';
    }

    protected function getTemplateData(): array
    {
        $cacheId = $this->getCacheId();
        $data = Cache::get($cacheId);

        if ($data) {
            return $data;
        }

        $data = ['selector_id' => null];
        $values = $this->getComponentData()->getValue();
        $selectorKey = $values->get('selector_id');

        /** @var \Kelnik\EstateVisual\Models\Selector $selector */
        $selector = resolve(SelectorRepository::class)->findByPrimary($selectorKey);
        $selectorService = resolve(SelectorService::class);

        $cacheTags = [
            $this->estateService->getModuleCacheTag(),
            $selectorService->getCacheTag($selector),
            resolve(PageService::class)->getPageComponentCacheTag($this->pageComponent->getKey())
        ];

        if (!$selector->exists || !$selector->active) {
            Cache::tags($cacheTags)->put($cacheId, $data, self::CACHE_TTL);

            return $data;
        }

        $data = [
            'title' => $values->get('title'),
            'selector_id' => $selectorKey,
            'step' => $this->getStepInfo($selector),
            'template' => $values->get('template'),
            'plural' => self::getPlural($values->get('types') ?? []),
            'url' => route(
                'kelnik.estateVisual.getData',
                [
                    'id' => $selectorKey,
                    'cid' => $this->pageComponent->getKey()
                ],
                false
            ),
            'baseUrl' => Route::current()->getCompiled()->getStaticPrefix(),
            'assets' => resolve(VisualService::class)->getAssets(),
            'callbackForm' => $this->getCallbackForm($this->getComponentData()->getValue()?->get('form', []))
        ];

        if ($data['callbackForm']) {
            $data['callbackForm']->pageComponentId = $this->pageComponent->getKey();
            $data['callbackForm']->slug = $this->getComponentData()->getPopupId($data['callbackForm']->primary);
        }

        Cache::tags($cacheTags)->put($cacheId, $data, self::CACHE_TTL);

        return $data;
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();

        abort_if(!$data['selector_id'], Response::HTTP_NOT_FOUND);

        $template = $data['template'] ?? self::getTemplates()->first();
        unset($data['template']);

        return view($template, $data);
    }

    public static function getTemplates(): Collection
    {
        return new Collection([
            new SelectorTemplate(
                'kelnik-estate-visual::components.selector.residential',
                trans('kelnik-estate-visual::admin.components.selector.templates.residential')
            )
        ]);
    }

    public function getCacheId(): string
    {
        return $this->estateService->getPremisesCacheTag('page_' . $this->page->getKey() . '_estate_visual');
    }
}
