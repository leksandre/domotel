<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Platform\Components;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\View;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Platform\Components\Contracts\PlatformComponent;

final class DurationInfo extends PlatformComponent implements KelnikComponentAlias
{
    private History $history;

    public function __construct(History $value)
    {
        $this->history = $value;
    }

    public static function getAlias(): string
    {
        return 'kelnik-estate-import-platform-duration';
    }

    public function render(): Closure|Htmlable|View|string
    {
        if ($this->history->state->isNew() || !$this->history->created_at || !$this->history->updated_at) {
            return '-';
        }

        return $this->history->result
            ? view(
                'kelnik-estate-import::platform.components.duration-info',
                ['history' => $this->history]
            )
            : '';
    }
}
