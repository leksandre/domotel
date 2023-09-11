<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Platform\Screens;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\FBlock\Platform\Services\Contracts\BlockPlatformService;

abstract class Screen extends \Orchid\Screen\Screen
{
    protected readonly CoreService $coreService;
    protected readonly BlockPlatformService $blockPlatformService;
    protected ?string $name = null;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->blockPlatformService = resolve(BlockPlatformService::class);
    }
}
