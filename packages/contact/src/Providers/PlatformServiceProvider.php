<?php

declare(strict_types=1);

namespace Kelnik\Contact\Providers;

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
            Menu::make(trans('kelnik-contact::admin.menu.title'))
                ->icon('bs.database')
                ->sort(1710)
                ->active([
                    $coreService->getFullRouteName('contact.office.list'),
                    $coreService->getFullRouteName('contact.office.edit'),
                    $coreService->getFullRouteName('contact.social.list'),
                    $coreService->getFullRouteName('contact.social.edit')
                ])
                ->permission(ContactServiceProvider::MODULE_PERMISSION)
                ->list([
                    Menu::make('kelnik-contact::admin.menu.offices')
                        ->icon('bs.geo-alt')
                        ->route($coreService->getFullRouteName('contact.office.list'))
                        ->active([
                            $coreService->getFullRouteName('contact.office.list'),
                            $coreService->getFullRouteName('contact.office.edit')
                        ])
                        ->permission(ContactServiceProvider::MODULE_PERMISSION),
                    Menu::make('kelnik-contact::admin.menu.social')
                        ->icon('bs.link')
                        ->route($coreService->getFullRouteName('contact.social.list'))
                        ->active([
                            $coreService->getFullRouteName('contact.social.list'),
                            $coreService->getFullRouteName('contact.social.edit')
                        ])
                        ->permission(ContactServiceProvider::MODULE_PERMISSION)
                ]),
        ];
    }

    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(trans('kelnik-contact::admin.menu.title'))
                ->addPermission(ContactServiceProvider::MODULE_PERMISSION, trans('kelnik-contact::admin.permission'))
        ];
    }
}
