<?php

declare(strict_types=1);

namespace Kelnik\Page\Providers;

use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Kelnik\Core\Providers\Contracts\KelnikModule;
use Kelnik\Page\Models\Contracts\PageMeta;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\Repositories\Contracts\PageComponentRepository;
use Kelnik\Page\Repositories\Contracts\PageComponentRouteElementRepository;
use Kelnik\Page\Repositories\Contracts\PageComponentRouteRepository;
use Kelnik\Page\Repositories\Contracts\PageRepository;
use Kelnik\Page\Repositories\KelnikBladeComponentRepository;
use Kelnik\Page\Repositories\PageComponentEloquentRepository;
use Kelnik\Page\Repositories\PageComponentRouteElementEloquentRepository;
use Kelnik\Page\Repositories\PageComponentRouteEloquentRepository;
use Kelnik\Page\Repositories\PageEloquentRepository;
use Kelnik\Page\Services\Contracts\HttpErrorService;
use Kelnik\Page\Services\Contracts\PageComponentBuffer;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\About\About;
use Kelnik\Page\View\Components\ErrorInfo\ErrorInfo;
use Kelnik\Page\View\Components\FirstScreen\FirstScreen;
use Kelnik\Page\View\Components\Footer\Footer;
use Kelnik\Page\View\Components\Gallery\Gallery;
use Kelnik\Page\View\Components\Header\Header;
use Kelnik\Page\View\Components\IconBlock\IconBlock;
use Kelnik\Page\View\Components\Infrastructure\Infrastructure;
use Kelnik\Page\View\Components\Location\Location;
use Kelnik\Page\View\Components\Stub\Stub;
use Kelnik\Page\View\Components\Usp\Usp;
use Kelnik\Page\View\Composers\BodyAttributes;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class PageServiceProvider extends KelnikModule
{
    public const VERSION = '1.8.5';
    public const MODULE_NAME = 'page';
    public const MODULE_PERMISSION = 'module.page.access';

    public array $bindings = [
        PageRepository::class => PageEloquentRepository::class,
        PageComponentRepository::class => PageComponentEloquentRepository::class,
        PageComponentRouteRepository::class => PageComponentRouteEloquentRepository::class,
        PageComponentRouteElementRepository::class => PageComponentRouteElementEloquentRepository::class,
        BladeComponentRepository::class => KelnikBladeComponentRepository::class,

        PageLinkService::class => \Kelnik\Page\Services\PageLinkService::class,
        PageService::class => \Kelnik\Page\Services\PageService::class,

        HttpErrorService::class => \Kelnik\Page\Services\HttpErrorService::class,

        PageMeta::class => \Kelnik\Page\Models\PageMeta::class
    ];

    public function getName(): string
    {
        return self::MODULE_NAME;
    }

    public function getTitle(): string
    {
        return trans('kelnik-page::admin.moduleName');
    }

    public function register(): void
    {
        $this->registerTranslate();

        foreach ($this->provides() as $provider) {
            $this->app->register($provider);
        }

        $this->app->singleton(
            PageComponentBuffer::class,
            static fn() => \Kelnik\Page\Services\PageComponentBuffer::getInstance()
        );

        /** @var Handler $exceptionHandler */
        $exceptionHandler = resolve(ExceptionHandler::class);

        $exceptionHandler->renderable(function (HttpExceptionInterface $e, $request) {
            /** @var HttpErrorService $httpErrorService */
            $httpErrorService = resolve(HttpErrorService::class);

            return $httpErrorService->executable($e, $request)
                ? $httpErrorService->handle($e, $request)
                : null;
        });
    }

    public function boot(): void
    {
        $this->registerViews();
        $this->registerMigrations();

        $this->loadRoutesFrom(__DIR__  . '/../../routes/web.php');

        $this->registerComponents();

        $this->publishes([
            __DIR__ . '/../../lang' => resource_path('lang/vendor/kelnik-page')
        ]);

        View::composer(['kelnik-page::app'], BodyAttributes::class);
    }

    protected function registerComponents(): void
    {
        Blade::components([
            About::class => About::getAlias(),
            Infrastructure::class => Infrastructure::getAlias(),
            FirstScreen::class => FirstScreen::getAlias(),
            Footer::class => Footer::getAlias(),
            Gallery::class => Gallery::getAlias(),
            Header::class => Header::getAlias(),
            Location::class => Location::getAlias(),
            Stub::class => Stub::getAlias(),
            Usp::class => Usp::getAlias(),
            IconBlock::class => IconBlock::getAlias(),

            ErrorInfo::class => ErrorInfo::getAlias()
        ]);
    }

    protected function registerTranslate(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'kelnik-page');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'kelnik-page');
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/kelnik-page')
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
