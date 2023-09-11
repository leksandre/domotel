<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Repositories\Contracts\BuildingRepository;
use Kelnik\Estate\Repositories\Contracts\ComplexRepository;
use Orchid\Support\Facades\Toast;

final class BuildingPlatformService implements Contracts\BuildingPlatformService
{
    public const NO_VALUE = '0';

    public function __construct(private BuildingRepository $repository, private CoreService $coreService)
    {
    }

    public function save(Building $building, Request $request): RedirectResponse
    {
        $request->validate([
            'building.complex_id' => 'required|numeric',
            'building.completion_id' => 'nullable|numeric',
            'building.title' => 'required|max:255',
            'building.active' => 'boolean',
            'building.complex_plan_image_id' => 'nullable|numeric',
            'building.external_id' => 'nullable|max:255',
            'building.floor_min' => 'numeric',
            'building.floor_max' => 'numeric'
        ]);

        $data = Arr::get($request->only('building'), 'building');

        $building->fill($data);
        $building->complex()->disassociate();
        $complex = resolve(ComplexRepository::class)->findByPrimary($data['complex_id']);

        if ($complex->exists) {
            $building->complex()->associate($complex);
        }
        unset($data);

        $this->repository->save($building);

        Toast::info(trans('kelnik-contact::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('estate.building.list'));
    }

    public function remove(Building $building, string $backRoute): RedirectResponse
    {
        $this->repository->delete($building)
            ? Toast::info(trans('kelnik-estate::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName($backRoute));
    }
}
