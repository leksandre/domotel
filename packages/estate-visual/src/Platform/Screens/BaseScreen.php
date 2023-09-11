<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Screens;

use Kelnik\Core\Services\Contracts\CoreService;
use Orchid\Screen\Screen;

abstract class BaseScreen extends Screen
{
    protected CoreService $coreService;
    protected ?string $name = null;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
    }
}
