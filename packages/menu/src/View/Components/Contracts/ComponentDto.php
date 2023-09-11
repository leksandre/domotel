<?php

declare(strict_types=1);

namespace Kelnik\Menu\View\Components\Contracts;

abstract class ComponentDto
{
    public int|string $primary = 0;
    public int $pageId = 0;
    public int $pageComponentId = 0;
    public ?string $template = null;
    public array $templateData = [];
    public array $cacheTags = [];
}
