<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Screens\Site;

use Kelnik\Core\Services\Contracts\CoreService;

abstract class Screen extends \Orchid\Screen\Screen
{
    protected readonly CoreService $coreService;
    protected ?string $name = null;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
    }
}
