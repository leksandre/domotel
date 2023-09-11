<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\View\Components\Contracts;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Kelnik\EstateVisual\Models\Contracts\SearchConfig;

interface RenderIframe
{
    public function getIframe(Request $request, SearchConfig $config): View;
}
