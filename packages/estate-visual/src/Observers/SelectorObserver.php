<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Observers;

use Kelnik\EstateVisual\Events\SelectorEvent;
use Kelnik\EstateVisual\Models\Selector;

final class SelectorObserver
{
    public function created(Selector $selector): void
    {
        $this->eventHandler($selector, __FUNCTION__);
    }

    public function updated(Selector $selector): void
    {
        $this->eventHandler($selector, __FUNCTION__);
    }

    public function deleted(Selector $selector): void
    {
        $this->eventHandler($selector, __FUNCTION__);
    }

    public function forceDelete(Selector $selector): void
    {
        $this->eventHandler($selector, __FUNCTION__);
    }

    protected function eventHandler(Selector $selector, string $methodName): void
    {
        SelectorEvent::dispatch($selector, $methodName);
    }
}
