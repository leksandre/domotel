<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Contracts;

use Kelnik\Page\Models\Contracts\ComponentDataProvider;

interface KelnikComponentDataProvider
{
    public static function initDataProvider(): ComponentDataProvider;
}
