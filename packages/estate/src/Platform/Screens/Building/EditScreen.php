<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\Building;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Platform\Layouts\Building\EditBaseLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Platform\Services\Contracts\BuildingPlatformService;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;

final class EditScreen extends BaseScreen
{
    private bool $exists = false;
    private ?string $title = null;
    protected string $routeToList = 'estate.building.list';
    private BuildingPlatformService $buildingPlatformService;

    public ?Building $building = null;

    public function __construct()
    {
        parent::__construct();
        $this->buildingPlatformService = resolve(BuildingPlatformService::class);
    }

    public function query(Building $building): array
    {
        $this->name = trans('kelnik-estate::admin.menu.buildings');
        $this->exists = $building->exists;

        if ($this->exists) {
            $this->name = $building->title;
        }

        return [
            'building' => $building
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-estate::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName($this->routeToList)),

            Button::make(trans('kelnik-estate::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeRow')
                ->confirm(trans('kelnik-estate::admin.deleteConfirm', ['title' => $this->title]))
                ->canSee($this->exists),
        ];
    }

    /** @return Layout[] */
    public function layout(): array
    {
        return [
            \Orchid\Support\Facades\Layout::tabs([
                trans('kelnik-estate::admin.tab.base') => EditBaseLayout::class
            ])
        ];
    }

    public function saveRow(Request $request): RedirectResponse
    {
        return $this->buildingPlatformService->save($this->building, $request);
    }

    public function removeRow(Building $building): RedirectResponse
    {
        return $this->buildingPlatformService->remove($building, $this->routeToList);
    }
}
