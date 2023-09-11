<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Section;
use Kelnik\Estate\Repositories\Contracts\SectionRepository;

interface SectionPlatformService
{
    public function __construct(SectionRepository $repository, CoreService $coreService);

    public function save(Section $section, Request $request): RedirectResponse;

    public function remove(Section $section, string $backRoute): RedirectResponse;
}
