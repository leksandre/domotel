<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Repositories\Contracts\ComplexRepository;
use Orchid\Support\Facades\Toast;

final class ComplexPlatformService implements Contracts\ComplexPlatformService
{
    public function __construct(private ComplexRepository $repository, private CoreService $coreService)
    {
    }

    public function save(Complex $complex, Request $request): RedirectResponse
    {
        $request->validate([
            'complex.title' => 'required|max:255',
            'complex.active' => 'boolean',
            'complex.external_id' => 'nullable|max:255',
            'complex.floor_min' => 'numeric',
            'complex.floor_max' => 'numeric'
        ]);

        $complex->fill(
            Arr::get($request->only('complex'), 'complex')
        );

        $this->repository->save($complex);

        Toast::info(trans('kelnik-contact::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('estate.complex.list'));
    }

    public function remove(Complex $complex, string $backRoute): RedirectResponse
    {
        $this->repository->delete($complex)
            ? Toast::info(trans('kelnik-estate::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName($backRoute));
    }
}
