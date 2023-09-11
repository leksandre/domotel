<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\Floor;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Platform\Layouts\Floor\EditBaseLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Platform\Services\Contracts\FloorPlatformService;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;

final class EditScreen extends BaseScreen
{
    private bool $exists = false;
    private ?string $title = null;
    protected string $routeToList = 'estate.floor.list';
    private FloorPlatformService $floorPlatformService;

    public ?Floor $floor = null;

    public function __construct()
    {
        parent::__construct();
        $this->floorPlatformService = resolve(FloorPlatformService::class);
    }

    public function query(Floor $floor): array
    {
        $this->name = trans('kelnik-estate::admin.menu.floors');
        $this->exists = $floor->exists;

        if ($this->exists) {
            $this->name = $floor->title;
        }

        return [
            'floor' => $floor
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

    /** @return Layout[]|string[] */
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
        return $this->floorPlatformService->save($this->floor, $request);
    }

    public function removeRow(Floor $floor): RedirectResponse
    {
        return $this->floorPlatformService->remove($floor, $this->routeToList);
    }
}
