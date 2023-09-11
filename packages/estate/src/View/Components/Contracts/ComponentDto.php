<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\Contracts;

use Illuminate\Support\Collection;

abstract class ComponentDto
{
    public int|string $primary = 0;
    public int $pageId = 0;
    public int $pageComponentId = 0;
    public ?Collection $cardRoutes = null;
    public ?string $template = null;
}
