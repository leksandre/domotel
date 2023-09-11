<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Providers;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\App;
use Kelnik\Core\Providers\Contracts\KelnikModule;
use Kelnik\Pdf\Services\Contracts\PdfService;

final class PdfServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'pdf';

    public array $bindings = [
        PdfService::class => \Kelnik\Pdf\Services\PdfService::class
    ];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-pdf::admin.moduleName');
    }

    public function register(): void
    {
        $this->registerTranslate();
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'kelnik-pdf');
        $this->registerProviders();
    }

    public function boot(): void
    {
        $this->registerViews();
        $this->registerConfig();

        $this->publishes([
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-pdf')
        ]);

        if (App::runningInConsole()) {
            AboutCommand::add(
                'Drivers',
                'Pdf',
                config('kelnik-pdf.connections.' . config('kelnik-pdf.connection') . '.driver')
            );
        }
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-pdf');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'kelnik-pdf');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/kelnik-pdf')
        ]);
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('kelnik-pdf.php'),
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
            ScheduleServiceProvider::class
        ];
    }
}
