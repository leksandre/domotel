<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Completion;
use Kelnik\Estate\Repositories\Contracts\CompletionRepository;
use Orchid\Support\Facades\Toast;

final class CompletionPlatformService implements Contracts\CompletionPlatformService
{
    public function __construct(private CompletionRepository $repository, private CoreService $coreService)
    {
    }

    public function save(Completion $completion, Request $request): RedirectResponse
    {
        $request->validate([
            'completion.title' => 'required|max:255',
            'completion.event_date' => 'required|date',
            'completion.external_id' => 'nullable|max:255'
        ]);

        $completion->fill(
            Arr::get($request->only('completion'), 'completion')
        );

        $this->repository->save($completion);

        Toast::info(trans('kelnik-contact::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('estate.completion.list'));
    }

    public function remove(Completion $completion, string $backRoute): RedirectResponse
    {
        $this->repository->delete($completion)
            ? Toast::info(trans('kelnik-estate::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName($backRoute));
    }
}
