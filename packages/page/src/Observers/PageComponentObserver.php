<?php

declare(strict_types=1);

namespace Kelnik\Page\Observers;

use Kelnik\Page\Events\PageComponentEvent;
use Kelnik\Page\Models\PageComponent;

final class PageComponentObserver
{
    public bool $afterCommit = true;

    public function created(PageComponent $pageComponent): void
    {
        $this->handle($pageComponent, __FUNCTION__);
    }

    public function updated(PageComponent $pageComponent): void
    {
        $this->handle($pageComponent, __FUNCTION__);
    }

    public function deleted(PageComponent $pageComponent): void
    {
        $this->handle($pageComponent, __FUNCTION__);
    }

    protected function handle(PageComponent $pageComponent, string $method): void
    {
        PageComponentEvent::dispatch($pageComponent, $method);
    }
}
