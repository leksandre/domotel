<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Screens;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\News\Platform\Services\Contracts\NewsPlatformService;

abstract class Screen extends \Orchid\Screen\Screen
{
    protected readonly CoreService $coreService;
    protected readonly NewsPlatformService $newsPlatformService;
    protected ?string $name = null;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->newsPlatformService = resolve(NewsPlatformService::class);
    }
}
