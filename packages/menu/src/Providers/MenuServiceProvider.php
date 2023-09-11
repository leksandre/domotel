<?php

declare(strict_types=1);

namespace Kelnik\Menu\Providers;

use Illuminate\Support\Facades\Blade;
use Kelnik\Core\Providers\Contracts\KelnikModule;
use Kelnik\Menu\Repositories\Contracts\MenuRepository;
use Kelnik\Menu\Repositories\MenuEloquentRepository;
use Kelnik\Menu\Services\Contracts\MenuService;
use Kelnik\Menu\View\Components\Menu\Menu;

final class MenuServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'menu';
    public const MODULE_PERMISSION = 'module.menu.access';

    public array $bindings = [
        MenuRepository::class => MenuEloquentRepository::class,
        MenuService::class => \Kelnik\Menu\Services\MenuService::class
    ];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-menu::admin.moduleName');
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
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-menu')
        ]);
    }

    protected function registerComponents(): void
    {
        Blade::components([
            Menu::class => Menu::getAlias()
        ]);
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-menu');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'kelnik-menu');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/kelnik-menu')
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
