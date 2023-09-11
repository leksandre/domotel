<?php

declare(strict_types=1);

namespace Kelnik\Progress\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Kelnik\Core\Events\ModuleCleared;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Progress\Models\Album;
use Kelnik\Progress\Models\AlbumVideo;
use Kelnik\Progress\Models\Camera;
use Kelnik\Progress\Models\Group;
use Kelnik\Progress\Providers\ProgressServiceProvider;
use Orchid\Attachment\Models\Attachment;

final class ClearingModuleData implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;

    public function handle(): void
    {
        $models = [
            Album::class,
            AlbumVideo::class,
            Camera::class,
            Group::class
        ];

        /** @var Model $modelNamespace */
        foreach ($models as $modelNamespace) {
            $modelNamespace::query()->truncate();
        }

        resolve(AttachmentRepository::class)
            ->getByGroupName(ProgressServiceProvider::MODULE_NAME)
            ->each(static fn(Attachment $el) => $el->delete());

        ModuleCleared::dispatch(ProgressServiceProvider::MODULE_NAME);
    }
}
