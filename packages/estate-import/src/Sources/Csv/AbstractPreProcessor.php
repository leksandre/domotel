<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Csv;

use Illuminate\Support\Arr;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\PreProcessor\Contracts\Filter;
use Kelnik\EstateImport\PreProcessor\Contracts\Mapper;
use Kelnik\EstateImport\PreProcessor\Contracts\PreProcessor;
use Kelnik\EstateImport\PreProcessor\IsNotUniqueException;
use Kelnik\EstateImport\PreProcessor\MapperDto;
use Kelnik\EstateImport\PreProcessor\Traits\FilterModel;
use Throwable;

abstract class AbstractPreProcessor extends PreProcessor
{
    use FilterModel;

    protected const COUNT_FOR_FLUSH = 100;

    protected \Kelnik\EstateImport\Sources\Contracts\SourceType $sourceType;

    public function __construct(History $history)
    {
        parent::__construct($history);
        $this->sourceType = new SourceType();
    }

    protected function getFileName(): string
    {
        return 'data.csv';
    }

    protected function getFilesDirName(): string
    {
        return 'files';
    }

    /**
     * @throws IsNotUniqueException
     * @throws Throwable
     */
    public function execute(): bool
    {
        $this->logger->info('Run pre-import', ['class' => self::class]);
        $timeStart = now();
        $this->history->setStateIsPreProcess();
        $this->history->setResultForState([
            'time' => [
                'start' => $timeStart->getTimestamp()
            ]
        ]);
        $this->historyRepository->save($this->history);

        if (config('kelnik-estate-import.unique.enable')) {
            $this->checkUnique();
        }

        $this->logger->info('Starting parse CSV file');

        try {
            $cnt = $this->processFile($this->getFileName());
        } catch (Throwable $e) {
            $this->logger->error('CSV parsing error', ['error' => $e->__toString()]);
            throw $e;
        }

        $this->logger->info('Parsing complete');
        $timeFinish = now();
        $this->history->setResultForState([
            'time' => [
                'finish' => $timeFinish->getTimestamp(),
            ],
            'rows' => $cnt
        ]);

        $this->logger->info('Save pre-processor results', ['rows' => $cnt]);
        $this->history->setStateIsReady();
        $this->history->setResultForState(['time' => ['start' => now()->getTimestamp()]]);
        $this->historyRepository->save($this->history);
        $this->resetCache();
        $this->logger->info('Pre-processor is complete');
        unset($pData, $timeStart, $timeFinish);

        return true;
    }

    protected function processFile(?string $filename = null): bool|int
    {
        if (!$filename) {
            $this->logger->error('Empty file name', ['file' => $filename]);

            return false;
        }

        $path = $this->getHistoryDirName() . '/' . $filename;
        unset($filename);

        if (!$this->storage->exists($path)) {
            $this->logger->error('File not found', ['path' => $path]);

            return false;
        }

        $config = $this->getConfig();
        $mapperClass = $this->sourceType->getMapper();
        $columnCount = max(array_keys($config['cols'] ?? []));

        if (empty($mapperClass) || !is_a($mapperClass, Mapper::class, true)) {
            $this->logger->error('Mapper required', ['mapper' => $mapperClass]);
            return false;
        }

        $fp = $this->storage->readStream($path);
        $lines = 0;
        /** @var array $mapper */
        $mapper = (new $mapperClass(
            (array)Arr::get(
                $this->importSettingsService->getSourceParams($this->sourceType),
                'replacement.list'
            )
        ))();
        $mapperDto = new MapperDto();
        $mapperDto->logger = &$this->logger;
        $mapperDto->storage = &$this->storage;
        $mapperDto->historyDirPath = $this->getHistoryDirName() ?? '';
        $mapperDto->filesDirPath = implode('/', [$mapperDto->historyDirPath , $this->getFilesDirName() ?? '']);

        $filterClass = $this->sourceType->getFilter();
        $filter = $filterClass && is_a($filterClass, Filter::class, true) ? new $filterClass() : null;

        while (!feof($fp)) {
            $row = fgetcsv($fp, 4096, $config['delimiter'], $config['enclosure'], $config['escape']);

            if (!$row) {
                continue;
            }

            $lines++;

            if ($columnCount && count($row) < $columnCount) {
                $this->logger->error('Incorrect CSV row', $row);
                continue;
            }

            if ($config['header'] && $lines < 2) {
                continue;
            }

            if (!($lines % static::COUNT_FOR_FLUSH)) {
                $this->flushRows();
            }

            foreach ($row as $k => $v) {
                $extractor = $config['cols'][$k] ?? [];

                if (empty($extractor['class'])) {
                    continue;
                }

                try {
                    $row[$k] = call_user_func((new $extractor['class']()), $v, ...($extractor['params'] ?? []));
                } catch (Throwable $e) {
                    $this->logger->error(
                        'Value extractor error',
                        [
                            'column' => $k,
                            'extractor' => $extractor,
                            'error' => $e->getMessage()
                        ]
                    );
                }
            }

            $mapperDto->source = $row;

            foreach ($mapper as $model => $fields) {
                $data = [];
                foreach ($fields as $fieldName => $column) {
                    if (is_callable($column)) {
                        $mapperDto->result = $data;
                        $data[$fieldName] = call_user_func($column, $mapperDto);
                        continue;
                    }

                    if (array_key_exists($column, $row)) {
                        $data[$fieldName] = $row[$column];
                        continue;
                    }

//                    throw new InvalidArgumentException(
//                        'Column "' . $column . '" not found. Model: ' . $model . ' Field: ' . $fieldName
//                    );
                    $this->logger->error(
                        'Column "' . $column . '" not found',
                        ['Model' => $model, 'Field' => $fieldName]
                    );
                }

                if (!$this->filterModel($model, $data, $filter)) {
                    continue;
                }

                $this->addRow($model, $data);
            }
        }
        fclose($fp);
        unset($mapperDto);

        if ($this->rows) {
            $this->flushRows();
        }

        return $lines;
    }

    protected function getConfig(): array
    {
        $config = $this->sourceType->getConfig();
        $config['header'] = $config['header'] ?? true;
        $config['encode'] = $config['encode'] ?? 'UTF-8';
        $config['delimiter'] = $config['delimiter'] ?? ';';
        $config['enclosure'] = $config['enclosure'] ?? '"';
        $config['escape'] = $config['escape'] ?? '\\';

        return $config;
    }
}
