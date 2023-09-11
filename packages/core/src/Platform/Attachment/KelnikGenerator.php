<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Attachment;

use Illuminate\Support\Str;
use Orchid\Attachment\Engines\Generator;

final class KelnikGenerator extends Generator
{
    /**
     * @inheritdoc
     */
    public function path(): string
    {
        return Str::substr($this->name(), 0, 4) . '/' . Str::substr($this->time, -4, 4);
    }
}
