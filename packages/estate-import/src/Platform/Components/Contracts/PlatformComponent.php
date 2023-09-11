<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Platform\Components\Contracts;

use Illuminate\View\Component;

abstract class PlatformComponent extends Component
{
    public function getStateClass(string $state): string
    {
        $stateToColor = [
            'error' => 'text-danger',
            'process' => 'text-info',
            'done' => 'text-success'
        ];

        return $stateToColor[$state] ?? 'text-secondary';
    }
}
