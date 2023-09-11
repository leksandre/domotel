<?php

declare(strict_types=1);

namespace Kelnik\Document\Providers;

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
            Menu::make(trans('kelnik-document::admin.menu.title'))
                ->icon('bs.database')
                ->sort(1610)
                ->active([
                    $coreService->getFullRouteName('document.categories'),
                    $coreService->getFullRouteName('document.category'),
                    $coreService->getFullRouteName('document.groups'),
                    $coreService->getFullRouteName('document.group')
                ])
                ->permission(DocumentServiceProvider::MODULE_PERMISSION)
                ->list([
                    Menu::make('kelnik-document::admin.menu.groups')
                        ->icon('folder')
                        ->route($coreService->getFullRouteName('document.groups'))
                        ->active([
                            $coreService->getFullRouteName('document.groups'),
                            $coreService->getFullRouteName('document.group')
                        ])
                        ->permission(DocumentServiceProvider::MODULE_PERMISSION),
                    Menu::make('kelnik-document::admin.menu.categories')
                        ->title()
                        ->icon('bs.file-earmark')
                        ->route($coreService->getFullRouteName('document.categories'))
                        ->active([
                            $coreService->getFullRouteName('document.categories'),
                            $coreService->getFullRouteName('document.category')
                        ])
                        ->permission(DocumentServiceProvider::MODULE_PERMISSION)
                ])
        ];
    }

    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(trans('kelnik-document::admin.menu.title'))
                ->addPermission(DocumentServiceProvider::MODULE_PERMISSION, trans('kelnik-document::admin.permission'))
        ];
    }
}
