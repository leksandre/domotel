<?php

declare(strict_types=1);

namespace Kelnik\Page\Providers;

use Kelnik\Core\Models\Site;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Page\Platform\Services\Contracts\PagePlatformService;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

final class PlatformServiceProvider extends OrchidServiceProvider
{
    public array $bindings = [
        PagePlatformService::class => \Kelnik\Page\Platform\Services\PagePlatformService::class
    ];

    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        $dashboard->registerResource('stylesheets', mix('/css/app.css', 'vendor/kelnik-page'));
        $dashboard->registerResource('scripts', mix('/js/manifest.js', 'vendor/kelnik-page'));
        $dashboard->registerResource('scripts', mix('/js/app.js', 'vendor/kelnik-page'));
    }

    public function registerMainMenu(): array
    {
        /**
         * @var CoreService $coreService
         * @var SiteService $siteService
         */
        $coreService = resolve(CoreService::class);
        $siteService = resolve(SiteService::class);

        $list = [];

        /** @var Site $site */
        foreach ($siteService->getAll() as $site) {
            $list[] = Menu::make($site->title)
                ->permission(PageServiceProvider::MODULE_PERMISSION)
                ->icon($site->primary ? 'bs.star' : 'bs.file-earmark')
                ->route(
                    $coreService->getFullRouteName('page.list'),
                    ['site' => $site->getKey()]
                )
                ->active([
                    route(
                        $coreService->getFullRouteName('page.list'),
                        ['site' => $site->getKey()]
                    ),
                    trim(route(
                        $coreService->getFullRouteName('page.list'),
                        ['site' => $site->getKey()]
                    ), 'list') . '*'
                ])
                ->addBeforeRender(function () use ($site) {
                    if ($site->primary) {
                        $this->set('class', $this->get('class') . ' kelnik-site_primary');
                    }
                });
        }

        return [
            Menu::make(trans('kelnik-page::admin.menuTitle'))
                ->sort(150)
                ->icon('bs.files')
                ->permission(PageServiceProvider::MODULE_PERMISSION)
                //->route($coreService->getFullRouteName('page.list'))
                ->active([
                    $coreService->getFullRouteName('page.*')
                ])
                ->list($list)
                ->hideEmpty()
        ];
    }

    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(trans('kelnik-page::admin.menuTitle'))
                ->addPermission(PageServiceProvider::MODULE_PERMISSION, trans('kelnik-page::admin.permission'))
        ];
    }
}
