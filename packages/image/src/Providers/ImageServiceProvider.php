<?php

declare(strict_types=1);

namespace Kelnik\Image\Providers;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\App;
use Kelnik\Core\Providers\Contracts\KelnikModule;

final class ImageServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'image';

    /** @var string[] */
    private array $commands = [];

    public array $bindings = [];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-image::admin.moduleName');
    }

    public function register(): void
    {
        $this->registerTranslate();
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'kelnik-image');
        $this->registerProviders();
    }

    public function boot(): void
    {
        $this->registerConfig();
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');

        $this->publishes([
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-image')
        ]);

        if (App::runningInConsole()) {
            AboutCommand::add(
                'Drivers',
                'Image',
                config('kelnik-image.driver')
            );
        }
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-image');
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('kelnik-image.php'),
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
//            EventServiceProvider::class
        ];
    }
}
