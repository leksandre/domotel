<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\StepElementAngle;
use Kelnik\EstateVisual\Models\StepElementAngleMask;
use Kelnik\EstateVisual\Models\StepElementAnglePointer;
use Kelnik\EstateVisual\Providers\EstateVisualServiceProvider;
use Orchid\Attachment\Models\Attachment;

final class ClearingModuleData implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;

    public function handle(): void
    {
        $models = [
            Selector::class,
            StepElement::class,
            StepElementAngle::class,
            StepElementAngleMask::class,
            StepElementAnglePointer::class
        ];

        /** @var Model $modelNamespace */
        foreach ($models as $modelNamespace) {
            $modelNamespace::query()->truncate();
        }

        resolve(AttachmentRepository::class)
            ->getByGroupName(EstateVisualServiceProvider::MODULE_NAME)
            ->each(static fn(Attachment $el) => $el->delete());
    }
}
