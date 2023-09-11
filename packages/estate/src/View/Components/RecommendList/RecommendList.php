<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\RecommendList;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Kelnik\Estate\Repositories\Contracts\PremisesRepository;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\Estate\Services\Contracts\PlanoplanService;
use Kelnik\Estate\View\Components\PremisesCard\PremisesCardBufferDto;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Services\Contracts\PageComponentBuffer;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;

final class RecommendList extends KelnikPageComponent
{
    public const COUNT_MIN = 1;
    public const COUNT_MAX = 20;
    public const COUNT_DEFAULT = 3;

    /** @var int Excluded row primary value */
    private int $primary = 0;
    private int $pageId = 0;
    private ?string $template = null;
    private ?PremisesCardBufferDto $buffer = null;

    private readonly EstateService $estateService;
    private readonly PageService $pageService;
    private readonly PlanoplanService $planoplanService;

    public function __construct()
    {
        $this->estateService = resolve(EstateService::class);
        $this->pageService = resolve(PageService::class);
        $this->planoplanService = resolve(PlanoplanService::class);
    }

    public static function getModuleName(): string
    {
        return EstateServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-estate::admin.components.recommendList.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-estate-recommend-list';
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    protected function getTemplateData(): array
    {
        $this->buffer = resolve(PageComponentBuffer::class)->get(PremisesCardBufferDto::class);

        if (!$this->buffer) {
            return [];
        }

        $this->primary = $this->buffer->elementId;

        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $data = $this->getComponentData()?->getValue();
        $res['title'] = $data->get('title');
        $res['count'] = (int)$data->get('count', self::COUNT_DEFAULT);
        $res['template'] = self::getTemplates()->first(
            static fn(Template $el) => $el->name === ($data['template'] ?? '')
        )?->name ?? self::getTemplates()->first()->name;
        unset($data);

        $res['list'] = $this->getElements($res['count']);

        $cacheTags = [
            $this->pageService->getPageComponentCacheTag($this->pageComponent->getKey()),
            $this->estateService->getModuleCacheTag(),
            $this->estateService->getPremisesCacheTag($this->primary),
        ];

        if ($this->buffer->getCacheTags()) {
            $cacheTags = array_merge($cacheTags, $this->buffer->cacheTags);
            $cacheTags = array_unique($cacheTags);
        }

        $res['list']->each(function (Premises $el) use (&$cacheTags) {
            $cacheTags[] = $this->estateService->getPremisesCacheTag($el->getKey());
            if ($el->relationLoaded('planoplan') && $el->planoplan->isAvailable()) {
                $cacheTags[] = $this->planoplanService->getCacheTag($el->planoplan->getKey());
            }
        });

        foreach ($this->buffer->getCardRoutes() as $routeName) {
            $cacheTags[] = $this->pageService->getDynComponentCacheTag($routeName);
        }

        Cache::tags(array_unique($cacheTags))->put($cacheId, $res, $this->cacheTtl);

        return $res;
    }

    private function getElements(int $limit): Collection
    {
        $filter = new ListFilter();

        $filter->typeGroupKey = $this->buffer->typeGroupId;
        $filter->typeKey = $this->buffer->typeId;
        $filter->excludeKey = $this->buffer->elementId;
        $filter->floorNum = $this->buffer->floorNum;
        $filter->priceTotal = $this->buffer->priceTotal;
        $filter->areaTotal = $this->buffer->areaTotal;
        //$filter->features = $this->buffer->features;
        $filter->limit = $limit;

        $res = resolve(PremisesRepository::class)->getRecommends($filter);
        unset($filter);

        if ($res->count()) {
            $res = $this->estateService->preparePremises($res, new Collection($this->buffer->getCardRoutes()));
        }

        return $res;
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();

        return !empty($data['list']) && $data['list']->count()
            ? view($this->template ?? 'kelnik-estate::components.recommendList.residential', $data)
            : null;
    }

    public static function getTemplates(): Collection
    {
        return new Collection([
            new Template(
                'kelnik-estate::components.recommendList.residential',
                trans('kelnik-estate::admin.components.recommendList.templates.residential')
            ),
        ]);
    }

    public function getCacheId(): string
    {
        return $this->estateService->getPremisesCacheTag(
            'page_' . $this->pageId .
            '_recommend_' . $this->primary .
            '_' . md5(json_encode($this->buffer?->toArray()))
        );
    }
}
