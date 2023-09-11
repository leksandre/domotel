<?php

declare(strict_types=1);

namespace Kelnik\News\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Kelnik\Core\Events\ModuleCleared;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\News\Models\Category;
use Kelnik\News\Models\Element;
use Kelnik\News\Providers\NewsServiceProvider;
use Orchid\Attachment\Models\Attachment;

final class ClearingModuleData implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;

    public function handle(): void
    {
        $models = [
            Category::class,
            Element::class
        ];

        /** @var Model $modelNamespace */
        foreach ($models as $modelNamespace) {
            $modelNamespace::query()->truncate();
        }

        resolve(AttachmentRepository::class)
            ->getByGroupName(NewsServiceProvider::MODULE_NAME)
            ->each(static fn(Attachment $el) => $el->delete());

        ModuleCleared::dispatch(NewsServiceProvider::MODULE_NAME);
    }
}
