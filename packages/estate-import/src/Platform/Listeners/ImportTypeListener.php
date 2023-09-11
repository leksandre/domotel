<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Platform\Listeners;

use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateImport\Platform\Services\Contracts\ImportPlatformService;
use Kelnik\EstateImport\Platform\Traits\SettingsRepository;
use Kelnik\EstateImport\Services\Contracts\ImportSettingsService;
use Orchid\Screen\Layout;
use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Repository;
use Throwable;

final class ImportTypeListener extends Listener
{
    use SettingsRepository;

    /** @var string[] */
    protected $targets = [
        'settings.source'
    ];

    public function __construct(
        private readonly CoreService $coreService,
        private readonly ImportPlatformService $importPlatformService,
        private readonly ImportSettingsService $importSettingsService
    ) {
    }

    /** @return Layout[] */
    protected function layouts(): iterable
    {
        return $this->query->get('source')?->getPlatformLayouts() ?? [];
    }

    public function handle(Repository $repository, Request $request): Repository
    {
        try {
            $source = $this->importSettingsService->getSourceByName(
                trim($request->input('settings.source') ?? '')
            );

            $repository = new Repository(
                $this->getQueryData($source)
            );
        } catch (Throwable $throwable) {
        }

        return $repository;
    }
}
