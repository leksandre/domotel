<?php

declare(strict_types=1);

namespace Kelnik\Estate\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Kelnik\Core\Events\ModuleCleared;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Models\City;
use Kelnik\Estate\Models\Completion;
use Kelnik\Estate\Models\CompletionStage;
use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Models\ComplexStatus;
use Kelnik\Estate\Models\ComplexSubwayStationReference;
use Kelnik\Estate\Models\ComplexType;
use Kelnik\Estate\Models\District;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\HidePrice;
use Kelnik\Estate\Models\Planoplan;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Models\PremisesFeature;
use Kelnik\Estate\Models\PremisesFeatureGroup;
use Kelnik\Estate\Models\PremisesFeatureReference;
use Kelnik\Estate\Models\PremisesPlanType;
use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\Estate\Models\PremisesType;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Models\Section;
use Kelnik\Estate\Models\Stat;
use Kelnik\Estate\Models\SubwayLine;
use Kelnik\Estate\Models\SubwayStation;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Orchid\Attachment\Models\Attachment;

final class ClearingModuleData implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;

    public function handle(): void
    {
        $models = [
            Building::class,
            City::class,
            Completion::class,
            CompletionStage::class,
            Complex::class,
            ComplexStatus::class,
            ComplexSubwayStationReference::class,
            ComplexType::class,
            District::class,
            Floor::class,
            HidePrice::class,
            Planoplan::class,
            Premises::class,
            PremisesFeature::class,
            PremisesFeatureGroup::class,
            PremisesFeatureReference::class,
            PremisesPlanType::class,
            PremisesStatus::class,
            PremisesType::class,
            PremisesTypeGroup::class,
            Section::class,
            Stat::class,
            SubwayLine::class,
            SubwayStation::class
        ];

        /** @var Model $modelNamespace */
        foreach ($models as $modelNamespace) {
            $modelNamespace::query()->truncate();
        }

        resolve(AttachmentRepository::class)
            ->getByGroupName(EstateServiceProvider::MODULE_NAME)
            ->each(static fn(Attachment $el) => $el->delete());

        ClearingModuleCache::dispatch();
        ModuleCleared::dispatch(EstateServiceProvider::MODULE_NAME);
    }
}
