<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Screens;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Form\Platform\Services\Contracts\FormPlatformService;

abstract class Screen extends \Orchid\Screen\Screen
{
    protected readonly CoreService $coreService;
    protected readonly FormPlatformService $formPlatformService;
    protected ?string $name = null;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->formPlatformService = resolve(FormPlatformService::class);
    }
}
