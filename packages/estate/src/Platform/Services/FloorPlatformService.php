<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Repositories\Contracts\BuildingRepository;
use Kelnik\Estate\Repositories\Contracts\FloorRepository;
use Orchid\Support\Facades\Toast;

final class FloorPlatformService implements Contracts\FloorPlatformService
{
    public function __construct(private FloorRepository $repository, private CoreService $coreService)
    {
    }

    public function save(Floor $floor, Request $request): RedirectResponse
    {
        $request->validate([
            'floor.building_id' => 'required|numeric',
            'floor.number' => 'required|numeric',
            'floor.title' => 'required|max:255',
            'floor.active' => 'boolean',
            'floor.external_id' => 'nullable|max:255'
        ]);

        $data = Arr::get($request->only('floor'), 'floor');

        $floor->fill($data);
        $floor->building()->disassociate();
        $building = resolve(BuildingRepository::class)->findByPrimary($data['building_id']);

        if ($building->exists) {
            $floor->building()->associate($building);
        }
        unset($data);

        $this->repository->save($floor);

        Toast::info(trans('kelnik-contact::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('estate.floor.list'));
    }

    public function remove(Floor $floor, string $backRoute): RedirectResponse
    {
        $this->repository->delete($floor)
            ? Toast::info(trans('kelnik-estate::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName($backRoute));
    }
}
