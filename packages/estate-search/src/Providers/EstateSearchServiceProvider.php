<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Providers;

use Illuminate\Support\Facades\Blade;
use Kelnik\Core\Providers\Contracts\KelnikModule;
use Kelnik\EstateSearch\Models\Config;
use Kelnik\EstateSearch\Models\Contracts\Pagination;
use Kelnik\EstateSearch\Models\Contracts\SearchConfig;
use Kelnik\EstateSearch\Repositories\Contracts\SearchRepository;
use Kelnik\EstateSearch\Repositories\SearchEloquentRepository;
use Kelnik\EstateSearch\Services\Contracts\SearchConfigFactory;
use Kelnik\EstateSearch\Services\Contracts\SearchService;
use Kelnik\EstateSearch\View\Components\Search\Search;

final class EstateSearchServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'estate-search';
    public const MODULE_PERMISSION = 'module.estateSearch.access';

    public array $bindings = [
        SearchRepository::class => SearchEloquentRepository::class,
        SearchConfig::class => Config::class,
        Pagination::class => \Kelnik\EstateSearch\Models\Pagination::class,
        SearchConfigFactory::class => \Kelnik\EstateSearch\Services\SearchConfigFactory::class,
        SearchService::class => \Kelnik\EstateSearch\Services\SearchService::class
    ];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-estate-search::admin.moduleName');
    }

    public function register(): void
    {
        $this->registerTranslate();
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'kelnik-estate-search');

        foreach ($this->provides() as $provider) {
            $this->app->register($provider);
        }
    }

    public function boot(): void
    {
        $this->registerViews();
        $this->loadRoutesFrom(__DIR__  . '/../../routes/web.php');
        $this->loadRoutesFrom(__DIR__  . '/../../routes/api.php');

        $this->registerComponents();

        $this->publishes([
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-estate-search')
        ]);
    }

    protected function registerComponents(): void
    {
        Blade::components([
            Search::class => Search::getAlias()
        ]);
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-estate-search');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'kelnik-estate-search');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/kelnik-estate-search')
        ]);
    }

    /** @return array<int, class-string> */
    public function provides(): array
    {
        return [
//            EventServiceProvider::class,
            PlatformServiceProvider::class
        ];
    }
}
