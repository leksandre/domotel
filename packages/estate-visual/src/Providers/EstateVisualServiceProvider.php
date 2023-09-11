<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Providers;

use Illuminate\Support\Facades\Blade;
use Kelnik\Core\Providers\Contracts\KelnikModule;
use Kelnik\EstateVisual\Commands\CleanModule;
use Kelnik\EstateVisual\Jobs\ClearingModuleData;
use Kelnik\EstateVisual\Models\Config;
use Kelnik\EstateVisual\Models\Contracts\Position;
use Kelnik\EstateVisual\Models\Contracts\SearchConfig;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\BuildingRepository;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\CompletionRepository;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\ComplexRepository;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\FloorRepository;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\PremisesRepository;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\PremisesStatusRepository;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\PremisesTypeGroupRepository;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\SectionRepository;
use Kelnik\EstateVisual\Repositories\Contracts\SearchRepository;
use Kelnik\EstateVisual\Repositories\Contracts\SelectorRepository;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementAngleMaskRepository;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementAnglePointerRepository;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementAngleRepository;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementRepository;
use Kelnik\EstateVisual\Repositories\Estate\BuildingEloquentRepository;
use Kelnik\EstateVisual\Repositories\Estate\CompletionEloquentRepository;
use Kelnik\EstateVisual\Repositories\Estate\ComplexEloquentRepository;
use Kelnik\EstateVisual\Repositories\Estate\FloorEloquentRepository;
use Kelnik\EstateVisual\Repositories\Estate\PremisesEloquentRepository;
use Kelnik\EstateVisual\Repositories\Estate\PremisesStatusEloquentRepository;
use Kelnik\EstateVisual\Repositories\Estate\PremisesTypeGroupEloquentRepository;
use Kelnik\EstateVisual\Repositories\Estate\SectionEloquentRepository;
use Kelnik\EstateVisual\Repositories\SearchEloquentRepository;
use Kelnik\EstateVisual\Repositories\SelectorEloquentRepository;
use Kelnik\EstateVisual\Repositories\StepElementAngleEloquentRepository;
use Kelnik\EstateVisual\Repositories\StepElementAngleMaskEloquentRepository;
use Kelnik\EstateVisual\Repositories\StepElementAnglePointerEloquentRepository;
use Kelnik\EstateVisual\Repositories\StepElementEloquentRepository;
use Kelnik\EstateVisual\Services\Contracts\SearchConfigFactory;
use Kelnik\EstateVisual\Services\Contracts\SelectorService;
use Kelnik\EstateVisual\Services\Contracts\VisualService;
use Kelnik\EstateVisual\View\Components\Selector\Selector;
use Kelnik\EstateVisual\View\Components\SelectorFrame\SelectorFrame;

final class EstateVisualServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'estate-visual';
    public const MODULE_PERMISSION = 'module.estateVisual.access';

    public array $bindings = [
        CompletionRepository::class => CompletionEloquentRepository::class,
        ComplexRepository::class => ComplexEloquentRepository::class,
        BuildingRepository::class => BuildingEloquentRepository::class,
        SectionRepository::class => SectionEloquentRepository::class,
        FloorRepository::class => FloorEloquentRepository::class,
        PremisesRepository::class => PremisesEloquentRepository::class,
        PremisesStatusRepository::class => PremisesStatusEloquentRepository::class,
        PremisesTypeGroupRepository::class => PremisesTypeGroupEloquentRepository::class,
        SelectorRepository::class => SelectorEloquentRepository::class,
        StepElementRepository::class => StepElementEloquentRepository::class,
        StepElementAngleRepository::class => StepElementAngleEloquentRepository::class,
        StepElementAngleMaskRepository::class => StepElementAngleMaskEloquentRepository::class,
        StepElementAnglePointerRepository::class => StepElementAnglePointerEloquentRepository::class,
        SelectorService::class => \Kelnik\EstateVisual\Services\SelectorService::class,
        SearchRepository::class => SearchEloquentRepository::class,
        SearchConfig::class => Config::class,
        SearchConfigFactory::class => \Kelnik\EstateVisual\Services\SearchConfigFactory::class,
        VisualService::class => \Kelnik\EstateVisual\Services\VisualService::class,
        Position::class => \Kelnik\EstateVisual\Models\Position::class
    ];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-estate-visual::admin.moduleName');
    }

    public function register(): void
    {
        $this->registerTranslate();
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'kelnik-estate-visual');
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
        $this->loadRoutesFrom(__DIR__  . '/../../routes/api.php');

        $this->registerComponents();

        $this->publishes([
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-estate-visual')
        ]);
    }

    protected function registerComponents(): void
    {
        Blade::components([
            Selector::class => Selector::getAlias(),
            SelectorFrame::class => SelectorFrame::getAlias()
        ]);
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-estate-visual');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'kelnik-estate-visual');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/kelnik-estate-visual')
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
            __DIR__ . '/../../config/config.php' => config_path('kelnik-estate-visual.php'),
        ], 'config');
    }

    /** @return array<int, class-string> */
    public function provides(): array
    {
        return [
            EventServiceProvider::class,
            PlatformServiceProvider::class
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
