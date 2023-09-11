<?php

declare(strict_types=1);

namespace Kelnik\Document\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Kelnik\Core\Events\ModuleCleared;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Document\Models\Category;
use Kelnik\Document\Models\Element;
use Kelnik\Document\Models\Group;
use Kelnik\Document\Providers\DocumentServiceProvider;
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
            Element::class,
            Group::class
        ];

        /** @var Model $modelNamespace */
        foreach ($models as $modelNamespace) {
            $modelNamespace::query()->truncate();
        }

        resolve(AttachmentRepository::class)
            ->getByGroupName(DocumentServiceProvider::MODULE_NAME)
            ->each(static fn(Attachment $el) => $el->delete());
        ModuleCleared::dispatch(DocumentServiceProvider::MODULE_NAME);
    }
}
