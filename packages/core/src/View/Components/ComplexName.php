<?php

declare(strict_types=1);

namespace Kelnik\Core\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;

final class ComplexName extends Component implements KelnikComponentAlias
{
    public function __construct(private readonly SettingsService $settingsService)
    {
    }

    public static function getAlias(): string
    {
        return 'kelnik-core-complex-name';
    }

    public function render(): View|string|null
    {
        return $this->settingsService->getComplex()->get('name') ?: 'Multi.Kelnik';
    }
}
