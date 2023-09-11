<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\Planoplan;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Models\Section;
use Kelnik\Estate\Repositories\Contracts\FloorRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesRepository;
use Kelnik\Estate\Repositories\Contracts\SectionRepository;
use Orchid\Support\Facades\Toast;

final class PremisesPlatformService implements Contracts\PremisesPlatformService
{
    public function __construct(private PremisesRepository $repository, private CoreService $coreService)
    {
    }

    public function save(Premises $premises, Request $request): RedirectResponse
    {
        $this->validate($premises, $request);
        $data = Arr::get($request->only('premises'), 'premises');
        $data['additional_properties'] = isset($data['additional_properties'])
            ? array_values($data['additional_properties'])
            : [];
        $premises->fill(Arr::except($data, ['features', 'gallery']));
        $premises->floor()->disassociate();
        $premises->section()->disassociate();

        /** @var Floor $floor */
        $floor = resolve(FloorRepository::class)->findByPrimary($data['floor_id']);

        if ($floor->exists) {
            $premises->floor()->associate($floor);
        }

        /** @var Section $section */
        $section = isset($data['section_id'])
            ? resolve(SectionRepository::class)->findByPrimary($data['section_id'])
            : new Section();

        if ($section->exists) {
            if ($floor->exists && $section->building->isNot($floor->building)) {
                return back()->withErrors(
                    new MessageBag([
                        trans('kelnik-estate::admin.errors.sectionHasAnotherBuilding')
                    ])
                );
            }
            $premises->section()->associate($section);
        }
        unset($data);

        $this->repository->save(
            $premises,
            $request->input('premises.features') ?? [],
            $request->input('premises.gallery') ?? []
        );

        Toast::info(trans('kelnik-contact::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('estate.premises.list'));
    }

    private function validate(Premises $premises, Request $request): array
    {
        $validateRules = [
            'premises.type_id' => 'required|numeric',
            'premises.original_type_id' => 'nullable|numeric',

            'premises.status_id' => 'required|numeric',
            'premises.original_status_id' => 'nullable|numeric',

            'premises.floor_id' => 'required|numeric',
            'premises.section_id' => 'nullable|numeric',

            'premises.active' => 'boolean',

            'premises.rooms' => 'nullable|numeric',

            'premises.price' => 'required|numeric',
            'premises.price_total' => 'required|numeric',
            'premises.price_sale' => 'required|numeric',
            'premises.price_meter' => 'required|numeric',
            'premises.area_total' => 'required|numeric',
            'premises.area_living' => 'required|numeric',
            'premises.area_kitchen' => 'required|numeric',

            'premises.image_list_id' => 'nullable|numeric',
            'premises.image_plan_id' => 'nullable|numeric',
            'premises.image_3d_id' => 'nullable|numeric',
            'premises.image_on_floor_id' => 'nullable|numeric',

            'premises.external_id' => 'nullable|max:255',
            'premises.number' => 'nullable|max:' . Premises::NUMBER_MAX_LENGTH,
            'premises.number_on_floor' => 'nullable|numeric',
            'premises.title' => 'required|max:255',
            'premises.planoplan_code' => 'nullable|max:' . Planoplan::CODE_MAX_LENGTH,
            'premises.additional_properties' => 'nullable|array'
        ];

        if ($request->exists('premises.additional_properties')) {
            $validateRules['premises.additional_properties.*.key'] = 'required|distinct|max:150|regex:/[a-z0-9\-_]+/i';
            $validateRules['premises.additional_properties.*.title'] = 'required|max:255';
            $validateRules['premises.additional_properties.*.value'] = 'required|max:255';
        }

        return $request->validate($validateRules);
    }

    public function remove(Premises $premises, string $backRoute): RedirectResponse
    {
        $this->repository->delete($premises)
            ? Toast::info(trans('kelnik-estate::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName($backRoute));
    }
}
