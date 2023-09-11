<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\ElementCard;

use Illuminate\Support\Collection;
use Kelnik\Page\Models\Contracts\DynComponentDto;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;

final class ElementCardLinkDto extends DynComponentDto
{
    public ?string $className = ElementCard::class;

    public function getPageComponentRoutes(Page $page, PageComponent $pageComponent): Collection
    {
        return ($this->className::initRouteProvider($page, $pageComponent))->makeRoutesByParams([
            'prefix' => $this->routePrefix . '-',
            'ignore_page_slug' => $this->ignorePageSlug
        ]);
    }
}
