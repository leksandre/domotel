<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\View\Components\SelectorFrame;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Kelnik\Core\View\Components\Contracts\HasMargin;
use Kelnik\Core\View\Components\Traits\Margin;
use Kelnik\EstateVisual\Models\Contracts\SearchConfig;
use Kelnik\EstateVisual\Repositories\Contracts\SelectorRepository;
use Kelnik\EstateVisual\Services\Contracts\SelectorService;
use Kelnik\EstateVisual\Services\Contracts\VisualService;
use Kelnik\EstateVisual\View\Components\Contracts\AbstractSelector;
use Kelnik\EstateVisual\View\Components\Contracts\RenderIframe;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Services\Contracts\PageService;

final class SelectorFrame extends AbstractSelector implements RenderIframe, HasMargin
{
    use Margin;

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    public static function getTitle(): string
    {
        return trans('kelnik-estate-visual::admin.components.selectorFrame.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-estate-visual-selector-frame';
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
            'alias' => $values->get('alias'),
            'selector_id' => $selectorKey,
            'step' => $this->getStepInfo($selector),
            'frameTemplate' => $values->get('frameTemplate'),
            'template' => $values->get('template') ?? self::getTemplates()->first()->name,
            'plural' => self::getPlural($values->get('types') ?? []),
            'url' => route(
                'kelnik.estateVisual.frame',
                [
                    'id' => $selectorKey,
                    'cid' => $this->pageComponent->getKey(),
                    'iframe' => 1
                ],
                false
            ),
            'margin' => $values->get('margin') ?? []
        ];

        Cache::tags($cacheTags)->put($cacheId, $data, self::CACHE_TTL);

        return $data;
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();

        if (!$data['selector_id']) {
            return null;
        }

        $template = $data['frameTemplate'] ?? self::getFrameTemplates()->first()->name;
        unset($data['frameTemplate']);

        return view($template, $data);
    }

    public function getIframe(Request $request, SearchConfig $config): View
    {
        $callbackForm = $this->getCallbackForm($config->form);

        if ($callbackForm) {
            $callbackForm->slug = $config->popup ?? $this->getComponentData()->getPopupId($callbackForm->primary);
            $callbackForm->cacheTags = array_merge($callbackForm->cacheTags, $config->cacheTags);
        }

        return view(
            $config->template,
            [
                'url' => \route(
                    'kelnik.estateVisual.getData',
                    [
                        'id' => Route::current()->parameter('id'),
                        'cid' => Route::current()->parameter('cid')
                    ],
                    false
                ),
                'baseUrl' => $request->getRequestUri(),
                'plural' => $config->plural ?? '',
                'assets' => resolve(VisualService::class)->getAssets(),
                'iframeType' => $config->iframeType ?: DataProvider::IFRAME_TYPE_NARROW,
                'callbackForm' => $callbackForm
            ]
        );
    }

    public static function getFrameTemplates(): Collection
    {
        return new Collection([
            new SelectorTemplate(
                'kelnik-estate-visual::components.selectorFrame.frame',
                trans('kelnik-estate-visual::admin.components.selectorFrame.templates.frame')
            ),
            new SelectorTemplate(
                'kelnik-estate-visual::components.selectorFrame.frame-full-width',
                trans('kelnik-estate-visual::admin.components.selectorFrame.templates.frameFullWidth')
            )
        ]);
    }

    public static function getTemplates(): Collection
    {
        return new Collection([
            new SelectorTemplate(
                'kelnik-estate-visual::components.selectorFrame.residential',
                trans('kelnik-estate-visual::admin.components.selectorFrame.templates.residential')
            )
        ]);
    }

    public function getCacheId(): string
    {
        return $this->estateService->getPremisesCacheTag(
            'page_' . $this->page->getKey() .
            '_estate_visual_frame_' . $this->pageComponent->getKey()
        );
    }
}
