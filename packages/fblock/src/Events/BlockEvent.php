<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kelnik\Core\Events\Contracts\ModelEvent;
use Kelnik\FBlock\Models\FlatBlock;

final class BlockEvent extends ModelEvent
{
    use Dispatchable;

    public function __construct(public FlatBlock $block, public string $methodName)
    {
    }
}
