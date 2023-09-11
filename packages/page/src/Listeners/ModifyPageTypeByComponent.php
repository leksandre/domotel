<?php

declare(strict_types=1);

namespace Kelnik\Page\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Kelnik\Page\Events\PageComponentEvent;
use Kelnik\Page\Models\Enums\Type;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\View\Components\Contracts\ErrorComponent;

final class ModifyPageTypeByComponent implements ShouldQueue
{
    public function handle(PageComponentEvent $event): void
    {
        if (!is_a($event->pageComponent->component, ErrorComponent::class, true)) {
            return;
        }

        if ($event->method === $event::CREATED) {
            $this->setPageType($event->pageComponent, Type::Error);
            return;
        }

        if (in_array($event->method, [$event::DELETED, $event::FORCE_DELETED])) {
            $this->setPageType($event->pageComponent, Type::Simple);
        }
    }

    private function setPageType(PageComponent $component, Type $type): void
    {
        $component->page->type = $type;
        $component->page->save();
    }
}
