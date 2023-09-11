<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\PremisesPlanType;
use Kelnik\Estate\Repositories\Contracts\PremisesPlanTypeRepository;
use Orchid\Support\Facades\Toast;

final class PremisesPlanTypePlatformService implements
    Contracts\PremisesPlanTypePlatformService
{
    public function __construct(private PremisesPlanTypeRepository $repository, private CoreService $coreService)
    {
    }

    public function getList(): Collection
    {
        return $this->repository->getAllForAdmin()->pluck('title', 'id');
    }

    public function save(PremisesPlanType $premisesPlanType, Request $request): RedirectResponse
    {
        $request->validate([
            'type.title' => 'required|max:255',
            'type.external_id' => 'nullable|max:255'
        ]);

        $premisesPlanType->fill(
            Arr::get($request->only('type'), 'type')
        );

        $this->repository->save($premisesPlanType);

        Toast::info(trans('kelnik-contact::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('estate.pplantype.list'));
    }

    public function remove(PremisesPlanType $premisesPlanType, string $backRoute): RedirectResponse
    {
        $this->repository->delete($premisesPlanType)
            ? Toast::info(trans('kelnik-estate::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName($backRoute));
    }

    public function createSlugByTitle(string $title): string
    {
        return Str::slug($title);
    }
}
