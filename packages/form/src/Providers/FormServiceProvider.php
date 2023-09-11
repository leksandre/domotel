<?php

declare(strict_types=1);

namespace Kelnik\Form\Providers;

use Illuminate\Support\Facades\Blade;
use Kelnik\Core\Providers\Contracts\KelnikModule;
use Kelnik\Form\Repositories\Contracts\FormFieldRepository;
use Kelnik\Form\Repositories\Contracts\FormLogRepository;
use Kelnik\Form\Repositories\Contracts\FormRepository;
use Kelnik\Form\Repositories\FormEloquentRepository;
use Kelnik\Form\Repositories\FormFieldEloquentRepository;
use Kelnik\Form\Repositories\FormLogEloquentRepository;
use Kelnik\Form\Services\Contracts\FormBaseService;
use Kelnik\Form\Services\Contracts\FormService;
use Kelnik\Form\View\Components\Form\Form;

final class FormServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'form';
    public const MODULE_PERMISSION = 'module.form.access';

    public array $bindings = [
        FormRepository::class => FormEloquentRepository::class,
        FormFieldRepository::class => FormFieldEloquentRepository::class,
        FormLogRepository::class => FormLogEloquentRepository::class,
        FormBaseService::class => \Kelnik\Form\Services\FormBaseService::class,
        FormService::class => \Kelnik\Form\Services\FormService::class
    ];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-form::admin.moduleName');
    }

    public function register(): void
    {
        $this->registerTranslate();
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'kelnik-form');

        foreach ($this->provides() as $provider) {
            $this->app->register($provider);
        }
    }

    public function boot(): void
    {
        $this->registerViews();
        $this->registerMigrations();
        $this->loadRoutesFrom(__DIR__  . '/../../routes/web.php');
        $this->loadRoutesFrom(__DIR__  . '/../../routes/api.php');

        $this->registerComponents();

        $this->publishes([
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-form')
        ]);
    }

    protected function registerComponents(): void
    {
        Blade::components([
            Form::class => Form::getAlias()
        ]);
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-form');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'kelnik-form');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/kelnik-form')
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
