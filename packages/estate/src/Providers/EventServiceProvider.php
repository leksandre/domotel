<?php

declare(strict_types=1);

namespace Kelnik\Estate\Providers;

use Kelnik\Estate\Events\EstateModelEvent;
use Kelnik\Estate\Events\PlanoplanEvent;
use Kelnik\Estate\Listeners\DeletePageLink;
use Kelnik\Estate\Listeners\EstateModelModified;
use Kelnik\Estate\Listeners\PlanoplanCreated;
use Kelnik\Estate\Listeners\ResetPlanoplanCache;
use Kelnik\Estate\Listeners\ResetPremisesFeatureGroupCache;
use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\Planoplan;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Models\PremisesFeature;
use Kelnik\Estate\Models\PremisesFeatureGroup;
use Kelnik\Estate\Models\PremisesFeatureReference;
use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\Estate\Models\PremisesType;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Models\Section;
use Kelnik\Estate\Observers\EstateModelObserver;
use Kelnik\Estate\Observers\PlanoplanObserver;

final class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    protected $listen = [
        EstateModelEvent::class => [
            DeletePageLink::class,
            EstateModelModified::class,
            ResetPremisesFeatureGroupCache::class
        ],
        PlanoplanEvent::class => [
            PlanoplanCreated::class,
            ResetPlanoplanCache::class
        ]
    ];

    public function boot(): void
    {
        parent::boot();

        $tables = [
            Complex::class,
            Building::class,
            Section::class,
            Floor::class,
            Premises::class,
            PremisesStatus::class,
            PremisesType::class,
            PremisesTypeGroup::class,
            PremisesFeature::class,
            PremisesFeatureReference::class,
            PremisesFeatureGroup::class
        ];

        foreach ($tables as $table) {
            $table::observe(EstateModelObserver::class);
        }

        Planoplan::observe(PlanoplanObserver::class);
    }
}
