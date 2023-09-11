<?php

declare(strict_types=1);

namespace Kelnik\Form\Providers;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Form\Platform\Services\Contracts\FormPlatformService;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

final class PlatformServiceProvider extends OrchidServiceProvider
{
    public array $bindings = [
        FormPlatformService::class => \Kelnik\Form\Platform\Services\FormPlatformService::class
    ];

    public function registerMainMenu(): array
    {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        return [
            Menu::make(trans('kelnik-form::admin.menu.title'))
                ->icon('bs.database')
                ->sort(1510)
                ->active([
                    $coreService->getFullRouteName('form.list'),
                    $coreService->getFullRouteName('form.edit'),
                    $coreService->getFullRouteName('form.field.list'),
                    $coreService->getFullRouteName('form.field.edit'),
                    $coreService->getFullRouteName('form.log.list'),
                    $coreService->getFullRouteName('form.log.view'),
                    $coreService->getFullRouteName('form.logs.list'),
                    $coreService->getFullRouteName('form.logs.view')
                ])
                ->permission(FormServiceProvider::MODULE_PERMISSION)
                ->list([
                    Menu::make('kelnik-form::admin.menu.forms')
                        ->icon('bs.list-task')
                        ->route($coreService->getFullRouteName('form.list'))
                        ->active([
                            $coreService->getFullRouteName('form.list'),
                            $coreService->getFullRouteName('form.edit'),
                            $coreService->getFullRouteName('form.field.list'),
                            $coreService->getFullRouteName('form.field.edit'),
                            $coreService->getFullRouteName('form.log.list'),
                            $coreService->getFullRouteName('form.log.view')
                        ])
                        ->permission(FormServiceProvider::MODULE_PERMISSION),
                    Menu::make('kelnik-form::admin.menu.logsList')
                        ->icon('bs.database')
                        ->route($coreService->getFullRouteName('form.logs.list'))
                        ->active([
                            $coreService->getFullRouteName('form.logs.list'),
                            $coreService->getFullRouteName('form.logs.view')
                        ])
                        ->permission(FormServiceProvider::MODULE_PERMISSION)
                ])
        ];
    }

    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(trans('kelnik-form::admin.menu.title'))
                ->addPermission(FormServiceProvider::MODULE_PERMISSION, trans('kelnik-form::admin.permission'))
        ];
    }
}
