<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Section;
use Kelnik\Estate\Repositories\Contracts\BuildingRepository;
use Kelnik\Estate\Repositories\Contracts\SectionRepository;
use Orchid\Support\Facades\Toast;

final class SectionPlatformService implements Contracts\SectionPlatformService
{
    public function __construct(private SectionRepository $repository, private CoreService $coreService)
    {
    }

    public function save(Section $section, Request $request): RedirectResponse
    {
        $request->validate([
            'section.building_id' => 'required|numeric',
            'section.title' => 'required|max:255',
            'section.active' => 'boolean',
            'section.external_id' => 'nullable|max:255',
            'section.floor_min' => 'numeric',
            'section.floor_max' => 'numeric'
        ]);

        $data = Arr::get($request->only('section'), 'section');

        $section->fill($data);
        $section->building()->disassociate();
        $building = resolve(BuildingRepository::class)->findByPrimary($data['building_id']);

        if ($building->exists) {
            $section->building()->associate($building);
        }
        unset($data);

        $this->repository->save($section);

        Toast::info(trans('kelnik-contact::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('estate.section.list'));
    }

    public function remove(Section $section, string $backRoute): RedirectResponse
    {
        $this->repository->delete($section)
            ? Toast::info(trans('kelnik-estate::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName($backRoute));
    }
}
