<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Providers;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\FBlock\Platform\Services\Contracts\BlockPlatformService;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

final class PlatformServiceProvider extends OrchidServiceProvider
{
    public array $bindings = [
        BlockPlatformService::class => \Kelnik\FBlock\Platform\Services\BlockPlatformService::class
    ];

    public function registerMainMenu(): array
    {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        return [
            Menu::make(trans('kelnik-fblock::admin.menu.title'))
                ->icon('bs.database')
                ->sort(1110)
                ->active([
                    $coreService->getFullRouteName('fblock.elements'),
                    $coreService->getFullRouteName('fblock.element')
                ])
                ->permission(FBlockServiceProvider::MODULE_PERMISSION)
                ->list([
                    Menu::make('kelnik-fblock::admin.menu.elements')
                    ->icon('bs.image')
                    ->route($coreService->getFullRouteName('fblock.elements'))
                    ->active([
                        $coreService->getFullRouteName('fblock.elements'),
                        $coreService->getFullRouteName('fblock.element')
                    ])
                    ->permission(FBlockServiceProvider::MODULE_PERMISSION)
                ])
        ];
    }

    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(trans('kelnik-fblock::admin.menu.title'))
                ->addPermission(FBlockServiceProvider::MODULE_PERMISSION, trans('kelnik-fblock::admin.permission'))
        ];
    }
}
