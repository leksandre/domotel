<?php

declare(strict_types=1);

namespace Kelnik\Estate\Providers;

use Illuminate\Support\Facades\Blade;
use Kelnik\Core\Providers\Contracts\KelnikModule;
use Kelnik\Estate\Commands\CleanModule;
use Kelnik\Estate\Jobs\ClearingModuleData;
use Kelnik\Estate\Repositories\BuildingEloquentRepository;
use Kelnik\Estate\Repositories\CompletionEloquentRepository;
use Kelnik\Estate\Repositories\ComplexEloquentRepository;
use Kelnik\Estate\Repositories\Contracts\BuildingRepository;
use Kelnik\Estate\Repositories\Contracts\CompletionRepository;
use Kelnik\Estate\Repositories\Contracts\ComplexRepository;
use Kelnik\Estate\Repositories\Contracts\FloorRepository;
use Kelnik\Estate\Repositories\Contracts\PlanoplanRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesFeatureGroupRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesFeatureReferenceRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesFeatureRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesPlanTypeRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesStatusRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeGroupRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeRepository;
use Kelnik\Estate\Repositories\Contracts\SectionRepository;
use Kelnik\Estate\Repositories\Contracts\StatRepository;
use Kelnik\Estate\Repositories\FloorEloquentRepository;
use Kelnik\Estate\Repositories\PlanoplanEloquentRepository;
use Kelnik\Estate\Repositories\PremisesEloquentRepository;
use Kelnik\Estate\Repositories\PremisesFeatureEloquentRepository;
use Kelnik\Estate\Repositories\PremisesFeatureGroupEloquentRepository;
use Kelnik\Estate\Repositories\PremisesFeatureReferenceEloquentRepository;
use Kelnik\Estate\Repositories\PremisesPlanTypeEloquentRepository;
use Kelnik\Estate\Repositories\PremisesStatusEloquentRepository;
use Kelnik\Estate\Repositories\PremisesTypeEloquentRepository;
use Kelnik\Estate\Repositories\PremisesTypeGroupEloquentRepository;
use Kelnik\Estate\Repositories\SectionEloquentRepository;
use Kelnik\Estate\Repositories\StatEloquentRepository;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\Estate\Services\Contracts\PlanoplanService;
use Kelnik\Estate\Services\Contracts\StatService;
use Kelnik\Estate\View\Components\PremisesCard\PremisesCard;
use Kelnik\Estate\View\Components\RecommendList\RecommendList;
use Kelnik\Estate\View\Components\StatList\StatList;

final class EstateServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'estate';
    public const MODULE_PERMISSION = 'module.estate.access';

    public array $bindings = [
        BuildingRepository::class => BuildingEloquentRepository::class,
        CompletionRepository::class => CompletionEloquentRepository::class,
        ComplexRepository::class => ComplexEloquentRepository::class,
        FloorRepository::class => FloorEloquentRepository::class,
        PlanoplanRepository::class => PlanoplanEloquentRepository::class,
        PremisesRepository::class => PremisesEloquentRepository::class,
        PremisesStatusRepository::class => PremisesStatusEloquentRepository::class,
        PremisesPlanTypeRepository::class => PremisesPlanTypeEloquentRepository::class,
        PremisesFeatureRepository::class => PremisesFeatureEloquentRepository::class,
        PremisesFeatureGroupRepository::class => PremisesFeatureGroupEloquentRepository::class,
        PremisesFeatureReferenceRepository::class => PremisesFeatureReferenceEloquentRepository::class,
        PremisesTypeRepository::class => PremisesTypeEloquentRepository::class,
        PremisesTypeGroupRepository::class => PremisesTypeGroupEloquentRepository::class,
        SectionRepository::class => SectionEloquentRepository::class,
        StatRepository::class => StatEloquentRepository::class,
        EstateService::class => \Kelnik\Estate\Services\EstateService::class,
        PlanoplanService::class => \Kelnik\Estate\Services\PlanoplanService::class,
        StatService::class => \Kelnik\Estate\Services\StatService::class
    ];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-estate::admin.moduleName');
    }

    public function register(): void
    {
        $this->registerTranslate();
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'kelnik-estate');
        $this->commands(CleanModule::class);

        foreach ($this->provides() as $provider) {
            $this->app->register($provider);
        }
    }

    public function boot(): void
    {
        $this->registerViews();
        $this->registerMigrations();
        $this->loadRoutesFrom(__DIR__  . '/../../routes/web.php');

        $this->registerComponents();

        $this->publishes([
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-estate')
        ]);
    }

    protected function registerComponents(): void
    {
        Blade::components([
            PremisesCard::class => PremisesCard::getAlias(),
            RecommendList::class => RecommendList::getAlias(),
            StatList::class => StatList::getAlias()
        ]);
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-estate');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'kelnik-estate');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/kelnik-estate')
        ]);
    }

    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }

    /** @return array<int, class-string> */
    public function provides(): array
    {
        return [
            EventServiceProvider::class,
            PlatformServiceProvider::class,
            ScheduleServiceProvider::class
        ];
    }

    public function hasCleaner(): bool
    {
        return true;
    }

    public function getCleanerJobs(): array
    {
        return [ClearingModuleData::class];
    }
}
