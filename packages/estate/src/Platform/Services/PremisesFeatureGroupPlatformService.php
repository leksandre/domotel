<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\PremisesFeatureGroup;
use Kelnik\Estate\Repositories\Contracts\PremisesFeatureGroupRepository;
use Orchid\Support\Facades\Toast;

final class PremisesFeatureGroupPlatformService implements
    Contracts\PremisesFeatureGroupPlatformService
{
    public function __construct(
        private PremisesFeatureGroupRepository $groupRepository,
        private CoreService $coreService
    ) {
    }

    public function save(PremisesFeatureGroup $premisesFeatureGroup, Request $request): RedirectResponse
    {
        $request->validate([
            'feature.title' => 'required|max:255',
            'feature.general' => 'boolean',
            'feature.external_id' => 'nullable|max:255'
        ]);

        $groupData = Arr::get($request->only('feature'), 'feature');
        $features = $groupData['features'] ?? [];
        unset($groupData['features']);

        $premisesFeatureGroup->fill($groupData);

        // Types
        if ($features) {
            $request->validate(
                [
                    'feature.features.*.title' => 'required|max:255',
                    'feature.features.*.external_id' => 'nullable|max:255',
                    'feature.features.*.icon_id' => 'nullable|numeric',
                ]
            );
        }

        $this->groupRepository->save($premisesFeatureGroup, $features);

        Toast::info(trans('kelnik-contact::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('estate.pfeature.list'));
    }

    public function remove(PremisesFeatureGroup $premisesFeatureGroup, string $backRoute): RedirectResponse
    {
        $this->groupRepository->delete($premisesFeatureGroup)
            ? Toast::info(trans('kelnik-estate::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName($backRoute));
    }
}
