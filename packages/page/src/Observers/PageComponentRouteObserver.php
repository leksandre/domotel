<?php

declare(strict_types=1);

namespace Kelnik\Page\Observers;

use Kelnik\Page\Events\PageComponentRouteEvent;
use Kelnik\Page\Models\PageComponentRoute;

final class PageComponentRouteObserver
{
    public bool $afterCommit = true;

    public function created(PageComponentRoute $pageComponentRoute): void
    {
        $this->handle($pageComponentRoute, __FUNCTION__);
    }

    public function updated(PageComponentRoute $pageComponentRoute): void
    {
        $this->handle($pageComponentRoute, __FUNCTION__);
    }

    public function deleted(PageComponentRoute $pageComponentRoute): void
    {
        $this->handle($pageComponentRoute, __FUNCTION__);
    }

    protected function handle(PageComponentRoute $pageComponentRoute, string $method): void
    {
        PageComponentRouteEvent::dispatch($pageComponentRoute, $method);
    }
}
