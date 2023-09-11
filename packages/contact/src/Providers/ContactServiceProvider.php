<?php

declare(strict_types=1);

namespace Kelnik\Contact\Providers;

use Illuminate\Support\Facades\Blade;
use Kelnik\Contact\Repositories\Contracts\OfficeRepository;
use Kelnik\Contact\Repositories\Contracts\SocialLinkRepository;
use Kelnik\Contact\Repositories\OfficeEloquentRepository;
use Kelnik\Contact\Repositories\SocialLinkEloquentRepository;
use Kelnik\Contact\Services\Contracts\ContactService;
use Kelnik\Contact\View\Components\Offices\Offices;
use Kelnik\Contact\View\Components\Social\SocialLinks;
use Kelnik\Core\Providers\Contracts\KelnikModule;

final class ContactServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'contact';
    public const MODULE_PERMISSION = 'module.contact.access';

    public array $bindings = [
        OfficeRepository::class => OfficeEloquentRepository::class,
        SocialLinkRepository::class => SocialLinkEloquentRepository::class,
        ContactService::class => \Kelnik\Contact\Services\ContactService::class
    ];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-contact::admin.moduleName');
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
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-contact')
        ]);
    }

    protected function registerComponents(): void
    {
        Blade::components([
            Offices::class => Offices::getAlias(),
            SocialLinks::class => SocialLinks::getAlias()
        ]);
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-contact');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'kelnik-contact');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/kelnik-contact')
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
