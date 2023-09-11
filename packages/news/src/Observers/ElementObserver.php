<?php

declare(strict_types=1);

namespace Kelnik\News\Observers;

use Kelnik\News\Events\ElementEvent;
use Kelnik\News\Models\Element;

final class ElementObserver
{
    public function created(Element $element): void
    {
        $this->handle($element, __FUNCTION__);
    }

    public function updated(Element $element): void
    {
        $this->handle($element, __FUNCTION__);
    }

    public function deleted(Element $element): void
    {
        $this->handle($element, __FUNCTION__);
    }

    public function restored(Element $element): void
    {
        $this->handle($element, __FUNCTION__);
    }

    public function forceDeleted(Element $element): void
    {
        $this->handle($element, __FUNCTION__);
    }

    private function handle(Element $element, string $methodName): void
    {
        ElementEvent::dispatch($element, $methodName);
    }
}
