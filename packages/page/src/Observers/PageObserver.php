<?php

declare(strict_types=1);

namespace Kelnik\Page\Observers;

use Kelnik\Page\Events\PageEvent;
use Kelnik\Page\Models\Page;

final class PageObserver
{
    public bool $afterCommit = true;

    public function created(Page $page): void
    {
        $this->handle($page, __FUNCTION__);
    }

    public function updated(Page $page): void
    {
        $this->handle($page, __FUNCTION__);
    }

    public function deleted(Page $page): void
    {
        $this->handle($page, __FUNCTION__);
    }

    protected function handle(Page $page, string $method): void
    {
        PageEvent::dispatch($page, $method);
    }
}
