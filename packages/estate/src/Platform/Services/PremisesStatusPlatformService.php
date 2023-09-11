<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\Estate\Repositories\Contracts\PremisesStatusRepository;
use Orchid\Support\Facades\Toast;

final class PremisesStatusPlatformService implements
    Contracts\PremisesStatusPlatformService
{
    public function __construct(private PremisesStatusRepository $repository, private CoreService $coreService)
    {
    }

    public function getList(): Collection
    {
        return $this->repository->getAllForAdmin()->pluck('title', 'id');
    }

    public function save(PremisesStatus $premisesStatus, Request $request): RedirectResponse
    {
        $request->validate([
            'status.title' => 'required|max:255',
            'status.replace_id' => 'nullable|numeric',
            'status.external_id' => 'nullable|max:255'
        ]);

        $premisesStatus->fill(
            Arr::get($request->only('status'), 'status')
        );

        $this->repository->save($premisesStatus);

        Toast::info(trans('kelnik-contact::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('estate.pstatus.list'));
    }

    public function remove(PremisesStatus $premisesStatus, string $backRoute): RedirectResponse
    {
        $this->repository->delete($premisesStatus)
            ? Toast::info(trans('kelnik-estate::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName($backRoute));
    }
}
