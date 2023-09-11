<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Completion;
use Kelnik\Estate\Repositories\Contracts\CompletionRepository;

interface CompletionPlatformService
{
    public function __construct(CompletionRepository $repository, CoreService $coreService);

    public function save(Completion $completion, Request $request): RedirectResponse;

    public function remove(Completion $completion, string $backRoute): RedirectResponse;
}
