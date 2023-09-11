<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\ElementCard;

use Kelnik\News\View\Components\Contracts\Template;

final class ElementCardTemplate extends Template
{
    public string $otherListTemplate = 'kelnik-news::components.otherList.news';
}
