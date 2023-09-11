<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Contracts;

use DateInterval;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\View\Component;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;
use Kelnik\Core\View\Components\Contracts\KelnikComponentCache;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Services\Contracts\PageService;

abstract class KelnikPageComponent extends Component implements
    KelnikComponentAlias,
    KelnikComponentCache,
    KelnikComponentDataProvider,
    KelnikPageComponentSection
{
    protected const CACHE_TTL_DEFAULT = 864000; // 10 days

    /** Page object. Assigned by page controller */
    protected ?Page $page = null;

    /** PageComponent object. Assigned by page controller */
    protected ?PageComponent $pageComponent = null;

    /** Request object. Assigned by page controller */
    protected ?Request $request = null;

    protected DateTimeInterface|DateInterval|int $cacheTtl = self::CACHE_TTL_DEFAULT;

    protected function getComponentData(): ComponentDataProvider
    {
        return $this?->pageComponent?->data ?? static::initDataProvider();
    }

    abstract public static function getModuleName(): string;

    abstract public static function getTitle(): string;

    /** Prepares data for rendering */
    abstract protected function getTemplateData(): iterable;

    public function getCacheId(): string
    {
        return resolve(PageService::class)->getPageComponentCacheTag(
            $this?->page->id . '_' . $this?->pageComponent->id
        );
    }

    public static function getCode(): string
    {
        return md5(static::getAlias() . '|' . static::class);
    }

    public static function getPageComponentSection(): string
    {
        return static::PAGE_COMPONENT_SECTION_CONTENT;
    }

    public function setPageComponent(PageComponent &$pageComponent): void
    {
        $this->pageComponent = &$pageComponent;
    }

    public function setPage(Page &$page): void
    {
        $this->page = &$page;
    }

    public function setRequest(Request &$request): void
    {
        $this->request = &$request;
    }
}
