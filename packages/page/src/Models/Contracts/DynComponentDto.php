<?php

declare(strict_types=1);

namespace Kelnik\Page\Models\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;

abstract class DynComponentDto
{
    public string $pageTitle;
    public string $pageSlug;
    public ?string $className = null;
    public bool $ignorePageSlug = true;
    public ?string $routePrefix = null;
    public array $params = [];

    public function __construct(string $pageTitle, string $pageSlug)
    {
        $this->pageSlug = $pageSlug;
        $this->pageTitle = $pageTitle;
    }

    abstract public function getPageComponentRoutes(Page $page, PageComponent $pageComponent): Collection;
}
