<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Xml;

use Illuminate\Support\Facades\Auth;
use Kelnik\EstateImport\Services\Contracts\DownloadService;

final class RemoteFile extends AbstractPreProcessor
{
    public function prepareData(...$data): bool
    {
        $this->history->pre_processor = self::class;
        $userName = Auth::user()?->name;

        if (!$this->downloadRemoteFile($data['url'])) {
            return false;
        }

        $this->history->pre_processor_data = [
            'userName' => $userName,
            'url' => $data['url']
        ];

        return $this->historyRepository->save($this->history);
    }

    private function downloadRemoteFile(string $url): bool
    {
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            $this->saveErrorAndExit('File url is empty or is not valid');

            return false;
        }

        /** @var DownloadService $downloadService */
        $downloadService = resolve(
            DownloadService::class,
            [
                'logger' => $this->logger,
                'storage' => $this->storage,
                'dirPath' => $this->getHistoryDirName()
            ]
        );

        $fileData = $downloadService->download($url, $this->getFileName());

        if ($fileData === null) {
            $this->saveErrorAndExit('Error on file downloading');

            return false;
        }

        $this->history->hash = md5($this->storage->get($fileData['path']));
        $this->historyRepository->save($this->history);

        return true;
    }

    private function saveErrorAndExit(string $msg): void
    {
        $this->history->setResultForState([
            'time' => [
                'finish' => now()->getTimestamp()
            ]
        ]);

        $this->logger->error($msg);
        $this->history->setStateIsError();
        $this->history->setResultForState(['message' => $msg]);
        $this->historyRepository->save($this->history);
    }
}
