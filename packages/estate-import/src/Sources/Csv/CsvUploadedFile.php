<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Csv;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\EstateImport\PreProcessor\IsNotUniqueException;
use Orchid\Attachment\Models\Attachment;
use Throwable;
use ZipArchive;

final class CsvUploadedFile extends AbstractPreProcessor
{
    private const ARCHIVE_NAME = 'images.zip';

    /** @throws FileNotFoundException */
    public function prepareData(...$data): bool
    {
        $this->history->pre_processor = self::class;
        $userName = Auth::user()?->name;

        $importFileId = (int)array_shift($data);
        $archiveFileId = (int)array_shift($data);

        $attachments = resolve(AttachmentRepository::class)
                ->getByPrimary([$importFileId, $archiveFileId])
                ->pluck(null, 'id');

        /**
         * @var ?Attachment $archiveFile
         * @var ?Attachment $importFile
         */
        $archiveFile = $attachments[$archiveFileId] ?? null;
        $importFile = $attachments[$importFileId] ?? null;

        $this->history->hash = $importFile?->hash ?? null;
        $this->history->pre_processor_data = [
            'userName' => $userName,
            'importFileId' => $importFileId,
            'archiveFileId' => $archiveFileId
        ];

        if ($archiveFile) {
            $this->logger->info(
                'Archive uploaded',
                [
                    'User' => $userName,
                    'FileName' => $archiveFile->original_name,
                    'Size' => $archiveFile->size
                ]
            );
        }

        $this->logger->info(
            'Data file uploaded',
            [
                'User' => $userName,
                'FileName' => $importFile->original_name,
                'Size' => $importFile->size
            ]
        );

        return $this->historyRepository->save($this->history);
    }

    /**
     * @throws IsNotUniqueException
     * @throws FileNotFoundException
     * @throws Throwable
     */
    public function execute(): bool
    {
        $this->logger->info('Prepare files');

        if (!$this->prepareFiles()) {
            return false;
        }

        return parent::execute();
    }

    /** @throws FileNotFoundException */
    private function prepareFiles(): bool
    {
        $data = $this->history->pre_processor_data;

        $userName = Arr::get($data, 'userName');
        $importFileId = (int)Arr::get($data, 'importFileId', 0);
        $archiveFileId = (int)Arr::get($data, 'archiveFileId', 0);

        $attachments = resolve(AttachmentRepository::class)
            ->getByPrimary([$importFileId, $archiveFileId])
            ->pluck(null, 'id');

        /**
         * @var ?Attachment $archiveFile
         * @var ?Attachment $importFile
         */
        $archiveFile = $attachments[$archiveFileId] ?? null;
        $importFile = $attachments[$importFileId] ?? null;
        unset($attachments);

        if (!$importFile) {
            $this->history->setResultForState([
                'time' => [
                    'finish' => now()->getTimestamp()
                ]
            ]);

            $errMsg = 'Import file not found';
            $this->logger->error($errMsg, ['fileId' => $importFileId]);
            $this->history->setStateIsError();
            $this->history->setResultForState([
                'message' => $errMsg
            ]);

            return false;
        }

        if ($archiveFile) {
            $this->logger->info('Copy images archive to history folder');
            $this->extractArch($this->storage, $archiveFile);
            $this->logger->info(
                'Archive moved and extracted',
                [
                    'User' => $userName,
                    'FileName' => $archiveFile->original_name,
                    'Size' => $archiveFile->size
                ]
            );
            $this->logger->debug('Delete archive file from Attachments', $archiveFile->toArray());
            $archiveFile->delete();
            unset($importImagesArch, $archiveFile);
        }

        $dataFilePath = $this->getHistoryDirName() . '/' . $this->getFileName();

        if ($this->storage->exists($dataFilePath)) {
            $this->storage->delete($dataFilePath);
        }

        $srcStream = Storage::disk($importFile->disk)->readStream($importFile->physicalPath());

        if (!$srcStream) {
            fclose($srcStream);
            $this->logger->error('Can\'t read CSV file', $importFile->toArray());
        }

        $this->storage->writeStream($dataFilePath, $srcStream);
        fclose($srcStream);

        $this->logger->info(
            'Data file moved',
            [
                'User' => $userName,
                'FileName' => $importFile->original_name,
                'Size' => $importFile->size
            ]
        );

        $this->logger->debug('Delete import file from Attachments', $importFile->toArray());
        $importFile->delete();
        $this->historyRepository->save($this->history);

        return true;
    }

    /** @throws FileNotFoundException */
    private function extractArch(Filesystem $storage, Attachment $arch): bool
    {
        $historyIsLocal = config('kelnik-estate-import.storage.config.driver') === 'local';
        $attachmentIsLocal = config('filesystems.disks.' . $arch->disk . '.driver') === 'local';

        if (!$historyIsLocal || !$attachmentIsLocal) {
            // TODO: unzip arch for remote filesystem
            return false;
        }

        $archFilePath = $this->getHistoryDirName() . '/' . $this->getFilesDirName() . '/' . self::ARCHIVE_NAME;

        if ($this->storage->exists($archFilePath)) {
            $this->storage->delete($archFilePath);
        }

        $srcStream = Storage::disk($arch->disk)->readStream($arch->physicalPath());
        $storage->writeStream($archFilePath, $srcStream);
        fclose($srcStream);

        $localDirPath = $storage->path('');

        if (!str_ends_with($localDirPath, DIRECTORY_SEPARATOR)) {
            $localDirPath .= DIRECTORY_SEPARATOR;
        }

        $localDirPath .= $this->getHistoryDirName() . DIRECTORY_SEPARATOR . $this->getFilesDirName();

        $this->unzip(
            $localDirPath . DIRECTORY_SEPARATOR . self::ARCHIVE_NAME,
            $localDirPath
        );
        unset($localDirPath);

        $this->storage->delete($archFilePath);
        $this->normalizeFileNames($this->getHistoryDirName() . '/' . $this->getFilesDirName());

        return true;
    }

    private function unzip(string $archPath, string $dstPath): bool|string
    {
        $zip = new ZipArchive();

        if (!$zip->open($archPath, ZipArchive::RDONLY)) {
            return false;
        }

        // TODO: umask ?
        $res = $zip->extractTo($dstPath);
        $zip->close();

        return $res;
    }

    private function normalizeFileNames(string $dirPath): void
    {
        $macOsDir = $dirPath . DIRECTORY_SEPARATOR . '__MACOSX';

        if ($this->storage->exists($macOsDir)) {
            $this->storage->deleteDirectory($macOsDir);
        }

        foreach ($this->storage->allFiles($dirPath) as $filePath) {
            $fileInfo = pathinfo($filePath);
            $newFileName = trim($fileInfo['filename']) . '.' . trim($fileInfo['extension']);

            if ($fileInfo['basename'] === $newFileName) {
                continue;
            }

            $this->storage->move($filePath, $fileInfo['dirname'] . DIRECTORY_SEPARATOR . $newFileName);
        }
    }
}
