<?php

declare(strict_types=1);

namespace Kelnik\Core\Providers;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Kelnik\Core\Map\Contracts\Balloon;
use Kelnik\Core\Map\Contracts\Coords;
use Kelnik\Core\Models\Contracts\SiteSettings;
use Kelnik\Core\Providers\Contracts\KelnikModule;
use Kelnik\Core\Repositories\AttachmentEloquentRepository;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Repositories\Contracts\SiteRepository;
use Kelnik\Core\Repositories\SettingsEloquentRepository;
use Kelnik\Core\Repositories\SiteEloquentRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Core\Services\Contracts\UploadService;
use Kelnik\Core\View\Components\ComplexName;
use Kelnik\Core\View\Components\CookieNotice;
use Kelnik\Core\View\Components\FavIcon;
use Kelnik\Core\View\Components\JsCodes;
use Kelnik\Core\View\Components\MapSettings;
use Kelnik\Core\View\Components\Meta;
use Kelnik\Core\View\Components\Theme\ComponentTheme;
use Kelnik\Core\View\Components\Theme\GlobalTheme;
use Orchid\Platform\Http\Middleware\Access;

final class CoreServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'core';
    public const SETTING_PERMISSION = 'module.core.settings';
    public const SITE_PERMISSION = 'module.core.sites';
    public const DEVELOPER_PERMISSION = 'module.core.developer';

    /** @var string[] */
    private array $commands = [];

    public array $bindings = [
        SettingsRepository::class => SettingsEloquentRepository::class,
        AttachmentRepository::class => AttachmentEloquentRepository::class,
        SiteRepository::class => SiteEloquentRepository::class,
        CoreService::class => \Kelnik\Core\Services\CoreService::class,
        SettingsService::class => \Kelnik\Core\Services\SettingsService::class,
        SiteService::class => \Kelnik\Core\Services\SiteService::class,
        UploadService::class => \Kelnik\Core\Services\UploadService::class,

        Coords::class => \Kelnik\Core\Map\Coords::class,
        Balloon::class => \Kelnik\Core\Map\Balloon::class,
        SiteSettings::class => \Kelnik\Core\Models\SiteSettings::class
    ];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-core::admin.moduleName');
    }

    public function register(): void
    {
        $this->registerTranslate();
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'kelnik-core');
        $this->registerProviders();
    }

    public function boot(): void
    {
        /** @var Router $router */
        $router = $this->app['router'];
        $router->aliasMiddleware('platform.access', Access::class);

        $this->registerViews();
        $this->registerMigrations();
        $this->registerConfig();
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');

        $this->registerBladeMacros();
        $this->registerComponents();

        $this->publishes([
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-core')
        ]);

        if (!App::runningInConsole()) {
            return;
        }

        AboutCommand::add(
            'Modules',
            function () {
                $res = [];

                foreach ($this->getModules() as $provider) {
                    $res[Str::headline($provider->getName())] = 'v' . $provider::VERSION;
                }

                return $res;
            }
        );
    }

    protected function registerBladeMacros(): void
    {
        // Optional variable `$pushOnceId` for template
        //
        Blade::directive('pushonce', function ($expression) {
            $var = '$__env->{"__pushonce_" . md5($pushOnceId ?? __FILE__ . ":" . __LINE__)}';

            return "<?php if (!isset($var)): $var = true; \$__env->startPush($expression); ?>";
        });

        Blade::directive('endpushonce', function ($expression) {
            return '<?php $__env->stopPush(); endif; ?>';
        });
    }

    protected function registerComponents(): void
    {
        Blade::components([
            GlobalTheme::class => GlobalTheme::getAlias(),
            CookieNotice::class => CookieNotice::getAlias(),
            ComponentTheme::class => ComponentTheme::getAlias(),
            ComplexName::class => ComplexName::getAlias(),
            JsCodes::class => JsCodes::getAlias(),
            FavIcon::class => FavIcon::getAlias(),
            MapSettings::class => MapSettings::getAlias(),
            Meta::class => Meta::getAlias()
        ]);
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-core');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'kelnik-core');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/kelnik-core')
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
            __DIR__ . '/../../config/config.php' => config_path('kelnik-core.php'),
        ], 'config');
    }

    public function registerProviders(): void
    {
        foreach ($this->provides() as $provide) {
            $this->app->register($provide);
        }
    }

    /** @return array<int, class-string> */
    public function provides(): array
    {
        return [
            EventServiceProvider::class,
            PlatformServiceProvider::class
        ];
    }

    /** @return KelnikModule[] */
    public function getModules(): array
    {
        return $this->app->getProviders(KelnikModule::class);
    }
}
