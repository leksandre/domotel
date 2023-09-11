<?php

declare(strict_types=1);

namespace Kelnik\Document\Providers;

use Illuminate\Support\Facades\Blade;
use Kelnik\Core\Providers\Contracts\KelnikModule;
use Kelnik\Document\Commands\CleanModule;
use Kelnik\Document\Jobs\ClearingModuleData;
use Kelnik\Document\Repositories\CategoryEloquentRepository;
use Kelnik\Document\Repositories\Contracts\CategoryRepository;
use Kelnik\Document\Repositories\Contracts\ElementRepository;
use Kelnik\Document\Repositories\Contracts\GroupRepository;
use Kelnik\Document\Repositories\ElementEloquentRepository;
use Kelnik\Document\Repositories\GroupEloquentRepository;
use Kelnik\Document\Services\Contracts\DocumentService;
use Kelnik\Document\View\Components\StaticList\StaticList;

final class DocumentServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'document';
    public const MODULE_PERMISSION = 'module.document.access';

    public array $bindings = [
        CategoryRepository::class => CategoryEloquentRepository::class,
        ElementRepository::class => ElementEloquentRepository::class,
        GroupRepository::class => GroupEloquentRepository::class,
        DocumentService::class => \Kelnik\Document\Services\DocumentService::class
    ];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-document::admin.moduleName');
    }

    public function register(): void
    {
        $this->registerTranslate();
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
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-document')
        ]);
    }

    protected function registerComponents(): void
    {
        Blade::components([
            StaticList::class => StaticList::getAlias()
        ]);
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-document');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'kelnik-document');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/kelnik-document')
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
