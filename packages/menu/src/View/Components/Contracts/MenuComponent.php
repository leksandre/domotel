<?php

declare(strict_types=1);

namespace Kelnik\Menu\View\Components\Contracts;

use Illuminate\View\Component;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;
use Kelnik\Core\View\Components\Contracts\KelnikComponentCache;

abstract class MenuComponent extends Component implements KelnikComponentCache, KelnikComponentAlias
{
    abstract public function __construct(?ComponentDto $params = null);
}
