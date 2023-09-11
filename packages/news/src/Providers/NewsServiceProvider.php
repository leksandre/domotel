<?php

declare(strict_types=1);

namespace Kelnik\News\Providers;

use Illuminate\Support\Facades\Blade;
use Kelnik\Core\Providers\Contracts\KelnikModule;
use Kelnik\News\Commands\CleanModule;
use Kelnik\News\Jobs\ClearingModuleData;
use Kelnik\News\Models\Contracts\ElementButton;
use Kelnik\News\Models\Contracts\ElementMeta;
use Kelnik\News\Repositories\CategoryEloquentRepository;
use Kelnik\News\Repositories\Contracts\CategoryRepository;
use Kelnik\News\Repositories\Contracts\ElementRepository;
use Kelnik\News\Repositories\ElementEloquentRepository;
use Kelnik\News\Services\Contracts\NewsService;
use Kelnik\News\View\Components\Element\Element;
use Kelnik\News\View\Components\ElementCard\ElementCard;
use Kelnik\News\View\Components\OtherList\OtherList;
use Kelnik\News\View\Components\StaticList\StaticList;

final class NewsServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'news';
    public const MODULE_PERMISSION = 'module.news.access';

    public array $bindings = [
        CategoryRepository::class => CategoryEloquentRepository::class,
        ElementRepository::class => ElementEloquentRepository::class,
        NewsService::class => \Kelnik\News\Services\NewsService::class,

        ElementButton::class => \Kelnik\News\Models\ElementButton::class,
        ElementMeta::class => \Kelnik\News\Models\ElementMeta::class
    ];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-news::admin.moduleName');
    }

    public function register(): void
    {
        $this->registerTranslate();
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'kelnik-news');
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
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-news')
        ]);
    }

    protected function registerComponents(): void
    {
        Blade::components([
            Element::class => Element::getAlias(),
            ElementCard::class => ElementCard::getAlias(),
            StaticList::class => StaticList::getAlias(),
            OtherList::class => OtherList::getAlias()
        ]);
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-news');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'kelnik-news');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/kelnik-news')
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
            __DIR__ . '/../../config/config.php' => config_path('kelnik-news.php'),
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
