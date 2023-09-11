<?php

declare(strict_types=1);

namespace Kelnik\News\Observers;

use Kelnik\News\Events\CategoryEvent;
use Kelnik\News\Models\Category;

final class CategoryObserver
{
    public function created(Category $category): void
    {
        $this->handle($category, __FUNCTION__);
    }

    public function updated(Category $category): void
    {
        $this->handle($category, __FUNCTION__);
    }

    public function deleted(Category $category): void
    {
        $this->handle($category, __FUNCTION__);
    }

    public function restored(Category $category): void
    {
        $this->handle($category, __FUNCTION__);
    }

    public function forceDeleted(Category $category): void
    {
        $this->handle($category, __FUNCTION__);
    }

    private function handle(Category $category, string $event): void
    {
        CategoryEvent::dispatch($category, $event);
    }
}
