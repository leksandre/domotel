<?php

declare(strict_types=1);

namespace Kelnik\Form\View\Components\Form;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use InvalidArgumentException;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;
use Kelnik\Core\View\Components\Contracts\KelnikComponentCache;
use Kelnik\Form\Services\Contracts\FormBaseService;
use Kelnik\Form\Services\Contracts\FormService;
use Kelnik\Form\View\Components\Contracts\ComponentDto;
use Kelnik\Page\Services\Contracts\PageService;

final class Form extends Component implements KelnikComponentCache, KelnikComponentAlias
{
    protected const CACHE_TTL_DEFAULT = 864000; // 10 days

    protected DateTimeInterface|DateInterval|int $cacheTtl = self::CACHE_TTL_DEFAULT;

    private int|string $primary = 0;
    private int $pageComponentId = 0;
    private array $templateData = [];
    private ?string $buttonTemplate = null;
    private ?string $slug = null;
    private ?string $template = null;
    private array $cacheTags = [];
    private readonly FormBaseService $formBaseService;
    private readonly PageService $pageService;

    public function __construct(?ComponentDto $params = null)
    {
        foreach (get_object_vars($params) as $k => $v) {
            if ($v !== null) {
                $this->{$k} = $params->{$k};
            }
        }

        $this->primary = (int)$this->primary;
        $this->formBaseService = resolve(FormBaseService::class);
        $this->pageService = resolve(PageService::class);
    }

    public static function getAlias(): string
    {
        return 'kelnik-form';
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();

        return $data
            ? \Illuminate\Support\Facades\View::first(
                [$this->template, 'kelnik-form::form'],
                $data
            )
            : null;
    }

    private function getTemplateData(): array
    {
        if (!$this->primary) {
            return [];
        }

        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        try {
            $res = resolve(FormService::class, ['primary' => $this->primary])->build();
        } catch (InvalidArgumentException $e) {
            return [];
        }

        if ($this->slug) {
            $res['slug'] = $this->slug;
        }

        if ($this->templateData) {
            $res = array_merge($res, $this->templateData);
        }
        $res['buttonTemplate'] = $this->buttonTemplate;
        $policyPageId = $res['policy_page_id'];

        $this->cacheTags[] = $this->formBaseService->getCacheTag($this->primary ?? 0);

        if ($policyPageId) {
            $res['policyPageLink'] = $this->pageService->getPageUrlById($policyPageId);
            $this->cacheTags[] = $this->pageService->getPageCacheTag($policyPageId);
        }

        if ($this->pageComponentId) {
            $this->cacheTags[] = $this->pageService->getPageComponentCacheTag($this->pageComponentId);
        }

        Cache::tags(array_filter($this->cacheTags))->put($cacheId, $res, $this->getCacheTtl());

        return $res;
    }

    public function getCacheId(): string
    {
        return $this->formBaseService->getCacheTag(
            $this->primary . ($this->pageComponentId ? '_' . $this->pageComponentId : '') .
            '_' . md5($this->slug . '|' . json_encode($this->templateData) . '|' . $this->buttonTemplate)
        );
    }

    private function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }
}
