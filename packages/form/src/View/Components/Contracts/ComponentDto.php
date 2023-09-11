<?php

declare(strict_types=1);

namespace Kelnik\Form\View\Components\Contracts;

abstract class ComponentDto
{
    public int|string $primary = 0;
    public int $pageId = 0;
    public int $pageComponentId = 0;
    public array $cacheTags = [];
    public array $templateData = [];
    public ?string $buttonTemplate = null;
    public ?string $slug = null;
    public ?string $template = null;
}
