<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\OtherList;

use Illuminate\Support\Collection;
use Kelnik\News\View\Components\Contracts\ComponentDto;

final class OtherListDto extends ComponentDto
{
    public int $categoryId = 0;
    public int $count = 0;
    public ?string $title = null;

    public function __construct()
    {
        $this->cardRoutes = new Collection();
    }
}
