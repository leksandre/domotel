<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Platform\Services\Contracts;

use DateTimeInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateImport\Repositories\Contracts\HistoryRepository;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface ImportPlatformService
{
    public function __construct(
        HistoryRepository $repository,
        CoreService $coreService,
        SettingsRepository $settingsRepository
    );

    public function addImportFile(Request $request): RedirectResponse;

    public function downloadLogFile(string $logName): BinaryFileResponse;

    public function getLogLink(DateTimeInterface $dateTime): ?string;

    public function saveSourceList(Request $request): RedirectResponse;

    public function saveReplacementList(Request $request): RedirectResponse;

    public function saveSettings(Request $request): RedirectResponse;
}
