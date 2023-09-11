<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Screens;

use Illuminate\Http\RedirectResponse;
use Kelnik\Core\Platform\Layouts\Tools\ClearingLayout;
use Kelnik\Core\Platform\Requests\ClearingRequest;
use Kelnik\Core\Services\Contracts\CoreService;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;
use Throwable;

final class ToolsScreen extends Screen
{
    private readonly CoreService $coreService;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
    }

    public function query(): array
    {
        $this->name = trans('kelnik-core::admin.tools.title');

        $modules = [];

        foreach ($this->coreService->getModuleList() as $module) {
            if (!$module->hasCleaner()) {
                continue;
            }

            $modules[$module->getName()] = $module->getTitle();
        }

        return [
            'modules' => $modules
        ];
    }

    public function commandBar(): array
    {
        return [];
    }

    /** @return string[] */
    public function layout(): array
    {
        return [
            ClearingLayout::class
        ];
    }

    public function clearing(ClearingRequest $request): RedirectResponse
    {
        try {
            Toast::success(
                $this->coreService->clearingModules($request->getDto())->isPending()
                    ? trans('kelnik-core::admin.tools.clearing.exec.queue')
                    : trans('kelnik-core::admin.tools.clearing.exec.sync')
            );
        } catch (Throwable $throwable) {
            Toast::error($throwable->getMessage());
        }

        return back();
    }
}
