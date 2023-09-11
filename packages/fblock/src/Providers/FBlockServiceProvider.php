<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Providers;

use Illuminate\Support\Facades\Blade;
use Kelnik\Core\Providers\Contracts\KelnikModule;
use Kelnik\FBlock\Repositories\BlockEloquentRepository;
use Kelnik\FBlock\Repositories\Contracts\BlockRepository;
use Kelnik\FBlock\Services\Contracts\BlockService;
use Kelnik\FBlock\View\Components\BlockList\BlockList;

final class FBlockServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'fblock';
    public const MODULE_PERMISSION = 'module.fblock.access';

    public array $bindings = [
        BlockRepository::class => BlockEloquentRepository::class,
        BlockService::class => \Kelnik\FBlock\Services\BlockService::class
    ];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-fblock::admin.moduleName');
    }

    public function register(): void
    {
        $this->registerTranslate();

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
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-fblock')
        ]);
    }

    protected function registerComponents(): void
    {
        Blade::components([
            BlockList::class => BlockList::getAlias()
        ]);
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-fblock');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'kelnik-fblock');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/kelnik-fblock')
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
}
