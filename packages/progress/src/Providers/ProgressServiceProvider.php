<?php

declare(strict_types=1);

namespace Kelnik\Progress\Providers;

use Illuminate\Support\Facades\Blade;
use Kelnik\Core\Providers\Contracts\KelnikModule;
use Kelnik\Progress\Commands\CleanModule;
use Kelnik\Progress\Jobs\ClearingModuleData;
use Kelnik\Progress\Repositories\AlbumEloquentRepository;
use Kelnik\Progress\Repositories\CameraEloquentRepository;
use Kelnik\Progress\Repositories\Contracts\AlbumRepository;
use Kelnik\Progress\Repositories\Contracts\CameraRepository;
use Kelnik\Progress\Repositories\Contracts\GroupRepository;
use Kelnik\Progress\Repositories\GroupEloquentRepository;
use Kelnik\Progress\Services\Contracts\ProgressService;
use Kelnik\Progress\View\Components\Progress\Progress;

final class ProgressServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'progress';
    public const MODULE_PERMISSION = 'module.progress.access';

    public array $bindings = [
        AlbumRepository::class => AlbumEloquentRepository::class,
        CameraRepository::class => CameraEloquentRepository::class,
        GroupRepository::class => GroupEloquentRepository::class,
        ProgressService::class => \Kelnik\Progress\Services\ProgressService::class
    ];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-progress::admin.moduleName');
    }

    public function register(): void
    {
        $this->registerTranslate();
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'kelnik-progress');
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
        $this->loadRoutesFrom(__DIR__  . '/../../routes/api.php');
        $this->loadRoutesFrom(__DIR__  . '/../../routes/web.php');

        $this->registerComponents();

        $this->publishes([
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-progress')
        ]);
    }

    protected function registerComponents(): void
    {
        Blade::components([
            Progress::class => Progress::getAlias()
        ]);
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-progress');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'kelnik-progress');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/kelnik-progress')
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
            __DIR__ . '/../../config/config.php' => config_path('kelnik-progress.php'),
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
