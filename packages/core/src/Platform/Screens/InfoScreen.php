<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Screens;

use Illuminate\Support\Facades\Auth;
use Kelnik\Core\Platform\Layouts\Info\AboutLayout;
use Kelnik\Core\Platform\Layouts\Info\ModuleListLayout;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Services\Contracts\CoreService;
use Orchid\Screen\Repository;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Dashboard;
use PDO;

final class InfoScreen extends Screen
{
    private readonly CoreService $coreService;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
    }

    public function query(): array
    {
        $this->name = trans('kelnik-core::admin.about.title');

        return [
            'info' => new Repository([
                'laravel' => app()->version(),
                'orchid' => Dashboard::version(),
                'php' => phpversion(),
                'composer' => app()->make('composer')->getVersion(),
                'database' => app()->make('db')->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION),
                'environment' => app()->environment(),
                'mailer' => app()->make('mailer')->getSymfonyTransport()->__toString(),
                'debug' => app()->hasDebugModeEnabled(),
                'queue' =>  app()->make('queue')->getDefaultDriver()
            ]),
            'modules' => $this->coreService->getModuleList(),
            'isDeveloper' => Auth::user()->hasAccess(CoreServiceProvider::DEVELOPER_PERMISSION),
            'phpLink' => route($this->coreService->getFullRouteName('core.about.php'))
        ];
    }

    public function commandBar(): array
    {
        return [];
    }

    /** @return string[] */
    public function layout(): array
    {
        return [
            AboutLayout::class,
            ModuleListLayout::class
        ];
    }
}
