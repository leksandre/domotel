<?php

declare(strict_types=1);

namespace Kelnik\Progress\Providers;

use Kelnik\Core\Services\Contracts\CoreService;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

final class PlatformServiceProvider extends OrchidServiceProvider
{
    public function registerMainMenu(): array
    {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        return [
            Menu::make(trans(trans('kelnik-progress::admin.menu.title')))
                ->icon('bs.database')
                ->sort(1310)
                ->active([
                    $coreService->getFullRouteName('progress.albums'),
                    $coreService->getFullRouteName('progress.album'),
                    $coreService->getFullRouteName('progress.cameras'),
                    $coreService->getFullRouteName('progress.camera'),
                    $coreService->getFullRouteName('progress.groups'),
                    $coreService->getFullRouteName('progress.group')
                ])
                ->permission(ProgressServiceProvider::MODULE_PERMISSION)
                ->list([
                    Menu::make('kelnik-progress::admin.menu.groups')
                        ->icon('bs.folder')
                        ->route($coreService->getFullRouteName('progress.groups'))
                        ->active([
                            $coreService->getFullRouteName('progress.groups'),
                            $coreService->getFullRouteName('progress.group')
                        ])
                        ->permission(ProgressServiceProvider::MODULE_PERMISSION),
                    Menu::make('kelnik-progress::admin.menu.albums')
                        ->icon('bs.camera')
                        ->route($coreService->getFullRouteName('progress.albums'))
                        ->active([
                            $coreService->getFullRouteName('progress.albums'),
                            $coreService->getFullRouteName('progress.album')
                        ])
                        ->permission(ProgressServiceProvider::MODULE_PERMISSION),
                    Menu::make('kelnik-progress::admin.menu.cameras')
                        ->icon('bs.camera-video')
                        ->route($coreService->getFullRouteName('progress.cameras'))
                        ->active([
                            $coreService->getFullRouteName('progress.cameras'),
                            $coreService->getFullRouteName('progress.camera')
                        ])
                        ->permission(ProgressServiceProvider::MODULE_PERMISSION)
                ])
        ];
    }

    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(trans('kelnik-progress::admin.menu.title'))
                ->addPermission(ProgressServiceProvider::MODULE_PERMISSION, trans('kelnik-progress::admin.permission'))
        ];
    }
}
