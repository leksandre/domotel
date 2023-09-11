<?php

declare(strict_types=1);

namespace Kelnik\Core\Providers;

use App\Http\Middleware\TrimStrings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Kelnik\Core\Platform\Services\Contracts\SitePlatformService;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Platform\Services\Contracts\SettingsPlatformService;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Screen\Repository;
use Orchid\Screen\TD;

final class PlatformServiceProvider extends OrchidServiceProvider
{
    public array $bindings = [
        SettingsPlatformService::class => \Kelnik\Core\Platform\Services\SettingsPlatformService::class,
        SitePlatformService::class => \Kelnik\Core\Platform\Services\SitePlatformService::class
    ];

    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        $this->registerPlatformTdMacro();
        $dashboard->registerResource('stylesheets', mix('/css/app.css', 'vendor/kelnik-core'));
        $dashboard->registerResource('scripts', mix('/js/manifest.js', 'vendor/kelnik-core'));
        $dashboard->registerResource('scripts', mix('/js/vendor.js', 'vendor/kelnik-core'));
        $dashboard->registerResource('scripts', mix('/js/app.js', 'vendor/kelnik-core'));

        TrimStrings::skipWhen(function (Request $request) {
            return $request->is('admin/site/edit/*');
        });
    }

    public function registerMainMenu(): array
    {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        return [
            Menu::make('kelnik-core::admin.site.menu')
                ->title(trans('kelnik-core::admin.control'))
                ->icon('bs.window-stack')
                ->sort(100)
                ->active([
                    $coreService->getFullRouteName('site.list'),
                    $coreService->getFullRouteName('site.edit'),
                ])
                ->permission(CoreServiceProvider::SITE_PERMISSION)
                ->route($coreService->getFullRouteName('site.list')),

            Menu::make('kelnik-core::admin.settings.menu')
                ->icon('bs.gear')
                ->sort(120)
                ->permission(CoreServiceProvider::SETTING_PERMISSION)
                ->route($coreService->getFullRouteName('core.settings')),

            Menu::make('kelnik-core::admin.about.title')
                ->title(trans('kelnik-core::admin.system'))
                ->sort(10000)
                ->icon('bs.box')
                ->route($coreService->getFullRouteName('core.about')),

            Menu::make('kelnik-core::admin.tools.title')
                ->sort(10000)
                ->icon('bs.tools')
                ->route($coreService->getFullRouteName('core.tools'))
                ->permission(CoreServiceProvider::DEVELOPER_PERMISSION),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->sort(10010)
                ->route('platform.systems.users')
                ->permission('platform.systems.users'),

            Menu::make(__('Roles'))
                ->icon('bs.lock')
                ->sort(10020)
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),
        ];
    }

    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(__('Systems'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
            ItemPermission::group(trans('kelnik-core::admin.moduleName'))->addPermission(
                CoreServiceProvider::SETTING_PERMISSION,
                trans('kelnik-core::admin.permissions.settings')
            ),
            ItemPermission::group(trans('kelnik-core::admin.moduleName'))->addPermission(
                CoreServiceProvider::SITE_PERMISSION,
                trans('kelnik-core::admin.permissions.sites')
            ),
            ItemPermission::group(trans('kelnik-core::admin.moduleName'))->addPermission(
                CoreServiceProvider::DEVELOPER_PERMISSION,
                trans('kelnik-core::admin.permissions.pref')
            ),
        ];
    }

    /** Регистрирует расширения ячейки таблицы */
    protected function registerPlatformTdMacro(): void
    {
        TD::macro(
            'dateTimeString',
            function (string $format = 'd F Y, H:i') {
                /** @var TD $this */
                $columnName = $this->column;

                $this->render(
                    function (Model|Repository $data) use ($columnName, $format) {
                        $defValue = '-';
                        $columnValue = $data instanceof Model
                            ? $data->getAttribute($columnName)
                            : $data->get($columnName);

                        return $columnValue instanceof Carbon
                            ? $columnValue->translatedFormat($format)
                            : $defValue;
                    }
                );

                return $this;
            }
        );

        TD::macro(
            'colorBlock',
            function () {
                /** @var TD $this */
                $columnName = $this->column;

                $this->render(
                    function (Model|Repository $data) use ($columnName) {
                        return \view(
                            'kelnik-core::platform.color',
                            [
                                'color' => $data instanceof Model
                                    ? $data->getAttribute($columnName)
                                    : $data->get($columnName)
                            ]
                        );
                    }
                );

                return $this;
            }
        );

        TD::macro(
            'booleanState',
            function () {
                /** @var TD $this */
                $columnName = $this->column;

                $this->render(
                    function (Model|Repository $data) use ($columnName) {
                        return \view(
                            'kelnik-core::platform.booleanState',
                            [
                                'state' => $data instanceof Model
                                    ? $data->getAttribute($columnName)
                                    : $data->get($columnName)
                            ]
                        );
                    }
                );

                return $this;
            }
        );
    }
}
