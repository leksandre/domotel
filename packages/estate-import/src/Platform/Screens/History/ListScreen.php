<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Platform\Screens\History;

use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateImport\Platform\Layouts\History\ListLayout;
use Kelnik\EstateImport\Platform\Services\Contracts\ImportPlatformService;
use Kelnik\EstateImport\Repositories\Contracts\HistoryRepository;
use Orchid\Screen\Layout;
use Orchid\Screen\Screen;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ListScreen extends Screen
{
    public function __construct(
        private CoreService $coreService,
        private ImportPlatformService $historyPlatformService
    ) {
    }

    public function query(): array
    {
        $this->name = trans('kelnik-estate-import::admin.menu.history');

        $logPath = config('kelnik-estate-import.logging.config.path');

        /** @var Collection $history */
        $history = resolve(HistoryRepository::class)->getAdminList();
        $items = $history->items();

        foreach ($items as &$el) {
            $cDate = $el->created_at?->format('Y-m-d') ?? '_';
            $filePathInfo = pathinfo($logPath);
            $fileName = $filePathInfo['filename'] . '-' . $cDate . '.' . $filePathInfo['extension'];
            $filePath = str_replace($filePathInfo['basename'], $fileName, $logPath);

            $el->logFilePath = $filePath && file_exists($filePath)
                ? $fileName
                : false;
        }

        return [
            'coreService' => $this->coreService,
            'history' => $history
        ];
    }

    /** @return Layout[] */
    public function layout(): array
    {
        return [
            ListLayout::class
        ];
    }

    public function getLog(string $logName): BinaryFileResponse
    {
        return $this->historyPlatformService->downloadLogFile($logName);
    }
}
