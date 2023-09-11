<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\Element;

use Illuminate\Support\Collection;
use Kelnik\News\View\Components\Contracts\ComponentDto;

final class ElementDto extends ComponentDto
{
    public array $templateData = [];

    public function __construct()
    {
        $this->cardRoutes = new Collection();
    }
}
