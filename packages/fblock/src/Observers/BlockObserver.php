<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Observers;

use Kelnik\FBlock\Events\BlockEvent;
use Kelnik\FBlock\Models\FlatBlock;

final class BlockObserver
{
    public function created(FlatBlock $block): void
    {
        $this->handle($block, __FUNCTION__);
    }

    public function updated(FlatBlock $block): void
    {
        $this->handle($block, __FUNCTION__);
    }

    public function deleted(FlatBlock $block): void
    {
        $this->handle($block, __FUNCTION__);
    }

    public function restored(FlatBlock $block): void
    {
        $this->handle($block, __FUNCTION__);
    }

    public function forceDeleted(FlatBlock $block): void
    {
        $this->handle($block, __FUNCTION__);
    }

    private function handle(FlatBlock $block, string $methodName): void
    {
        BlockEvent::dispatch($block, $methodName);
    }
}
