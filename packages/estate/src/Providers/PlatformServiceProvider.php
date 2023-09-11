<?php

declare(strict_types=1);

namespace Kelnik\Estate\Providers;

use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Platform\Services\Contracts\BuildingPlatformService;
use Kelnik\Estate\Platform\Services\Contracts\CompletionPlatformService;
use Kelnik\Estate\Platform\Services\Contracts\ComplexPlatformService;
use Kelnik\Estate\Platform\Services\Contracts\EstatePlatformService;
use Kelnik\Estate\Platform\Services\Contracts\FloorPlatformService;
use Kelnik\Estate\Platform\Services\Contracts\ImportPlanPlatformService;
use Kelnik\Estate\Platform\Services\Contracts\PremisesFeatureGroupPlatformService;
use Kelnik\Estate\Platform\Services\Contracts\PremisesPlanTypePlatformService;
use Kelnik\Estate\Platform\Services\Contracts\PremisesPlatformService;
use Kelnik\Estate\Platform\Services\Contracts\PremisesStatusPlatformService;
use Kelnik\Estate\Platform\Services\Contracts\PremisesTypeGroupPlatformService;
use Kelnik\Estate\Platform\Services\Contracts\SectionPlatformService;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

final class PlatformServiceProvider extends OrchidServiceProvider
{
    public array $bindings = [
        BuildingPlatformService::class => \Kelnik\Estate\Platform\Services\BuildingPlatformService::class,
        CompletionPlatformService::class => \Kelnik\Estate\Platform\Services\CompletionPlatformService::class,
        ComplexPlatformService::class => \Kelnik\Estate\Platform\Services\ComplexPlatformService::class,
        EstatePlatformService::class => \Kelnik\Estate\Platform\Services\EstatePlatformService::class,
        FloorPlatformService::class => \Kelnik\Estate\Platform\Services\FloorPlatformService::class,
        PremisesFeatureGroupPlatformService::class =>
            \Kelnik\Estate\Platform\Services\PremisesFeatureGroupPlatformService::class,
        PremisesPlatformService::class => \Kelnik\Estate\Platform\Services\PremisesPlatformService::class,
        PremisesPlanTypePlatformService::class =>
            \Kelnik\Estate\Platform\Services\PremisesPlanTypePlatformService::class,
        PremisesStatusPlatformService::class => \Kelnik\Estate\Platform\Services\PremisesStatusPlatformService::class,
        PremisesTypeGroupPlatformService::class =>
            \Kelnik\Estate\Platform\Services\PremisesTypeGroupPlatformService::class,
        SectionPlatformService::class => \Kelnik\Estate\Platform\Services\SectionPlatformService::class,
        ImportPlanPlatformService::class => \Kelnik\Estate\Platform\Services\ImportPlanPlatformService::class
    ];

    public function registerMainMenu(): array
    {
        $coreService = resolve(CoreService::class);

        $complexActive = [
            $coreService->getFullRouteName('estate.complex.list'),
            $coreService->getFullRouteName('estate.complex.edit')
        ];
        $buildingActive = [
            $coreService->getFullRouteName('estate.building.list'),
            $coreService->getFullRouteName('estate.building.edit')
        ];
        $sectionActive = [
            $coreService->getFullRouteName('estate.section.list'),
            $coreService->getFullRouteName('estate.section.edit')
        ];
        $floorActive = [
            $coreService->getFullRouteName('estate.floor.list'),
            $coreService->getFullRouteName('estate.floor.edit')
        ];
        $premisesActive = [
            $coreService->getFullRouteName('estate.premises.list'),
            $coreService->getFullRouteName('estate.premises.edit')
        ];

        $completionActive = [
            $coreService->getFullRouteName('estate.completion.list'),
            $coreService->getFullRouteName('estate.completion.edit')
        ];
        $premisesStatusActive = [
            $coreService->getFullRouteName('estate.pstatus.list'),
            $coreService->getFullRouteName('estate.pstatus.edit')
        ];
        $premisesPlanTypeActive = [
            $coreService->getFullRouteName('estate.pplantype.list'),
            $coreService->getFullRouteName('estate.pplantype.edit')
        ];
        $premisesTypeActive = [
            $coreService->getFullRouteName('estate.ptype.list'),
            $coreService->getFullRouteName('estate.ptype.edit')
        ];
        $premisesFeatureActive = [
            $coreService->getFullRouteName('estate.pfeature.list'),
            $coreService->getFullRouteName('estate.pfeature.edit')
        ];

        return [
            Menu::make(trans('kelnik-estate::admin.menu.title'))
                ->slug('estate')
                ->icon('bs.database')
                ->sort(1810)
                ->permission(EstateServiceProvider::MODULE_PERMISSION)
                ->list([
                    Menu::make(trans('kelnik-estate::admin.menu.complexes'))
                        ->permission(EstateServiceProvider::MODULE_PERMISSION)
                        ->route($complexActive[0])
                        ->active($complexActive),
                    Menu::make(trans('kelnik-estate::admin.menu.buildings'))
                        ->permission(EstateServiceProvider::MODULE_PERMISSION)
                        ->route($buildingActive[0])
                        ->active($buildingActive),
                    Menu::make(trans('kelnik-estate::admin.menu.sections'))
                        ->permission(EstateServiceProvider::MODULE_PERMISSION)
                        ->route($sectionActive[0])
                        ->active($sectionActive),
                    Menu::make(trans('kelnik-estate::admin.menu.floors'))
                        ->permission(EstateServiceProvider::MODULE_PERMISSION)
                        ->route($floorActive[0])
                        ->active($floorActive),
                    Menu::make(trans('kelnik-estate::admin.menu.premises'))
                        ->permission(EstateServiceProvider::MODULE_PERMISSION)
                        ->route($premisesActive[0])
                        ->active($premisesActive),
                    Menu::make(trans('kelnik-estate::admin.menu.completions'))
                        ->permission(EstateServiceProvider::MODULE_PERMISSION)
                        ->route($completionActive[0])
                        ->active($completionActive)
                        ->title('kelnik-estate::admin.menu.referenceBook'),
                    Menu::make(trans('kelnik-estate::admin.menu.premisesStatuses'))
                        ->permission(EstateServiceProvider::MODULE_PERMISSION)
                        ->route($premisesStatusActive[0])
                        ->active($premisesStatusActive),
//                    Menu::make(trans('kelnik-estate::admin.menu.premisesPlanTypes'))
//                        ->permission(EstateServiceProvider::MODULE_PERMISSION)
//                        ->route($premisesPlanTypeActive[0])
//                        ->active($premisesPlanTypeActive),
                    Menu::make(trans('kelnik-estate::admin.menu.premisesTypes'))
                        ->permission(EstateServiceProvider::MODULE_PERMISSION)
                        ->route($premisesTypeActive[0])
                        ->active($premisesTypeActive),
                    Menu::make(trans('kelnik-estate::admin.menu.premisesFeatures'))
                        ->permission(EstateServiceProvider::MODULE_PERMISSION)
                        ->route($premisesFeatureActive[0])
                        ->active($premisesFeatureActive),
                    Menu::make(trans('kelnik-estate::admin.menu.importPlan'))
                        ->title('kelnik-estate::admin.menu.services')
                        ->icon('bs.crop')
                        ->permission(EstateServiceProvider::MODULE_PERMISSION)
                        ->route($coreService->getFullRouteName('estate.importPlan'))
                        ->active([$coreService->getFullRouteName('estate.importPlan')])
                ])->addBeforeRender(function () {
                    /** @var Collection $list */
                    $list = $this->get('list');

                    if ($list?->isEmpty()) {
                        return;
                    }

                    $active = [];
                    foreach ($list as $el) {
                        $active = array_merge($active, $el->get('active') ?? []);
                    }
                    $active = array_merge($this->get('active') ?? [], $active);
                    $active = array_unique($active);

                    $this->set('active', $active);
                })
        ];
    }

    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(trans('kelnik-estate::admin.menu.title'))
                ->addPermission(EstateServiceProvider::MODULE_PERMISSION, trans('kelnik-estate::admin.permission'))
        ];
    }
}
