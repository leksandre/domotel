<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\PremisesCard;

use Illuminate\Support\Collection;
use Kelnik\Page\Models\Contracts\DynComponentDto;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;

final class PremisesCardLinkDto extends DynComponentDto
{
    public ?string $className = PremisesCard::class;

    public function getPageComponentRoutes(Page $page, PageComponent $pageComponent): Collection
    {
        return ($this->className::initRouteProvider($page, $pageComponent))->makeRoutesByParams([
            'prefix' => $this->routePrefix . '-',
            'ignore_page_slug' => $this->ignorePageSlug
        ]);
    }
}
