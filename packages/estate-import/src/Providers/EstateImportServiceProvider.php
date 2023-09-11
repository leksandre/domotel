<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Providers;

use Illuminate\Support\Facades\Blade;
use Kelnik\Core\Providers\Contracts\KelnikModule;
use Kelnik\EstateImport\Commands\CleanModule;
use Kelnik\EstateImport\Jobs\ClearingModuleData;
use Kelnik\EstateImport\Platform\Components\DurationInfo;
use Kelnik\EstateImport\Processor\Contracts\Processor;
use Kelnik\EstateImport\Processor\ImportProcessor;
use Kelnik\EstateImport\Repositories\AttachmentEloquentRepository;
use Kelnik\EstateImport\Repositories\Contracts\AttachmentRepository;
use Kelnik\EstateImport\Repositories\Contracts\DataQueueRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BuildingRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\CompletionRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\ComplexRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\FloorRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesFeatureGroupRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesFeatureRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesStatusRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesTypeGroupRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesTypeRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\SectionRepository;
use Kelnik\EstateImport\Repositories\Contracts\HistoryRepository;
use Kelnik\EstateImport\Repositories\DataQueueEloquentRepository;
use Kelnik\EstateImport\Repositories\Estate\BuildingEloquentRepository;
use Kelnik\EstateImport\Repositories\Estate\CompletionEloquentRepository;
use Kelnik\EstateImport\Repositories\Estate\ComplexEloquentRepository;
use Kelnik\EstateImport\Repositories\Estate\FloorEloquentRepository;
use Kelnik\EstateImport\Repositories\Estate\PremisesEloquentRepository;
use Kelnik\EstateImport\Repositories\Estate\PremisesFeatureEloquentRepository;
use Kelnik\EstateImport\Repositories\Estate\PremisesFeatureGroupEloquentRepository;
use Kelnik\EstateImport\Repositories\Estate\PremisesStatusEloquentRepository;
use Kelnik\EstateImport\Repositories\Estate\PremisesTypeEloquentRepository;
use Kelnik\EstateImport\Repositories\Estate\PremisesTypeGroupEloquentRepository;
use Kelnik\EstateImport\Repositories\Estate\SectionEloquentRepository;
use Kelnik\EstateImport\Repositories\HistoryEloquentRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;
use Kelnik\EstateImport\Services\Contracts\DownloadService;
use Kelnik\EstateImport\Services\Contracts\ImportSettingsService;
use Kelnik\EstateImport\Sources\Allio\Contracts\AllioClient;
use Kelnik\EstateImport\Sources\Allio\Contracts\AllioConfig;
use Kelnik\EstateImport\Sources\ProfitBase\Contracts\ProfitBaseClient;
use Kelnik\EstateImport\Sources\ProfitBase\Contracts\ProfitBaseConfig;

final class EstateImportServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'estate-import';
    public const MODULE_PERMISSION = 'module.estateImport.access';

    public array $bindings = [
        HistoryRepository::class => HistoryEloquentRepository::class,
        DataQueueRepository::class => DataQueueEloquentRepository::class,
        Processor::class => ImportProcessor::class,

        AttachmentRepository::class => AttachmentEloquentRepository::class,
        CompletionRepository::class => CompletionEloquentRepository::class,
        ComplexRepository::class => ComplexEloquentRepository::class,
        BuildingRepository::class => BuildingEloquentRepository::class,
        SectionRepository::class => SectionEloquentRepository::class,
        FloorRepository::class => FloorEloquentRepository::class,
        PremisesRepository::class => PremisesEloquentRepository::class,
        PremisesFeatureGroupRepository::class => PremisesFeatureGroupEloquentRepository::class,
        PremisesFeatureRepository::class => PremisesFeatureEloquentRepository::class,
        PremisesStatusRepository::class => PremisesStatusEloquentRepository::class,
        PremisesTypeGroupRepository::class => PremisesTypeGroupEloquentRepository::class,
        PremisesTypeRepository::class => PremisesTypeEloquentRepository::class,

        // Services
        //
        CacheService::class => \Kelnik\EstateImport\Services\CacheService::class,
        DownloadService::class => \Kelnik\EstateImport\Services\DownloadService::class,
        ImportSettingsService::class => \Kelnik\EstateImport\Services\ImportSettingsService::class,

        // Clients
        //
        AllioConfig::class => \Kelnik\EstateImport\Sources\Allio\AllioConfig::class,
        AllioClient::class => \Kelnik\EstateImport\Sources\Allio\AllioClient::class,
        ProfitBaseConfig::class => \Kelnik\EstateImport\Sources\ProfitBase\ProfitBaseConfig::class,
        ProfitBaseClient::class => \Kelnik\EstateImport\Sources\ProfitBase\ProfitBaseClient::class
    ];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-estate-import::admin.moduleName');
    }

    public function register(): void
    {
        $this->registerTranslate();
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'kelnik-estate-import');
        $this->commands(CleanModule::class);

        foreach ($this->provides() as $provider) {
            $this->app->register($provider);
        }
    }

    public function boot(): void
    {
        $this->registerViews();
        $this->registerMigrations();
        $this->registerConfig();
        $this->loadRoutesFrom(__DIR__  . '/../../routes/web.php');

        $this->registerComponents();

        $this->publishes([
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-estate-import')
        ]);
    }

    protected function registerComponents(): void
    {
        Blade::components([
            DurationInfo::class => DurationInfo::getAlias()
        ]);
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-estate-import');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'kelnik-estate-import');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/kelnik-estate-import')
        ]);
    }

    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('kelnik-estate-import.php'),
        ], 'config');
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
