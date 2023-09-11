<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Xml;

use Exception;
use Illuminate\Support\Arr;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\PreProcessor\Contracts\Mapper;
use Kelnik\EstateImport\PreProcessor\Contracts\PreProcessor;
use Kelnik\EstateImport\PreProcessor\IsNotUniqueException;
use Kelnik\EstateImport\PreProcessor\MapperDto;
use Kelnik\EstateImport\PreProcessor\Traits\FilterModel;
use Kelnik\EstateImport\ValueExtractors\StringValueExtractor;
use League\Flysystem\Local\LocalFilesystemAdapter;
use SimpleXMLElement;
use Throwable;
use XMLReader;

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
        return 'data.xml';
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
            'time' => ['start' => $timeStart->getTimestamp()]
        ]);
        $this->historyRepository->save($this->history);

        if (config('kelnik-estate-import.unique.enable')) {
            $this->checkUnique();
        }

        $this->logger->info('Starting parse XML file');

        try {
            $cnt = $this->processFile($this->getFileName());
            if ($cnt === false) {
               throw new Exception('Empty data list');
            }
        } catch (Throwable $e) {
            $this->logger->error('XML parsing error', ['error' => $e->__toString()]);
            throw $e;
        }

        $this->logger->info('Parsing complete');
        $this->history->setResultForState([
            'time' => ['finish' => now()->getTimestamp(),],
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

        $fileUrl = 'file://' . $this->storage->path($path);

        if (!$this->storage->getAdapter() instanceof LocalFilesystemAdapter) {
            $fileUrl = $this->storage->url($path);
        }

        $mapperClass = $this->sourceType->getMapper();

        if (empty($mapperClass) || !is_a($mapperClass, Mapper::class, true)) {
            $this->logger->error('Mapper required', ['mapper' => $mapperClass]);

            return false;
        }

        /** @var XMLReader|bool $xml */
        $xml = XMLReader::open($fileUrl);

        if ($xml === false) {
            $this->logger->error('XMLReader error on opening file', ['fileUrl' => $fileUrl]);

            return false;
        }

        $mapper = (new $mapperClass(
            (array)Arr::get(
                $this->importSettingsService->getSourceParams($this->sourceType),
                'replacement.list'
            )
        ))();
        $keys = array_keys($mapper);
        $lines = 0;

        $mapperDto = new MapperDto();
        $mapperDto->logger = &$this->logger;
        $mapperDto->storage = &$this->storage;
        $mapperDto->historyDirPath = $this->getHistoryDirName() ?? '';
        $mapperDto->filesDirPath = implode('/', [$mapperDto->historyDirPath , $this->getFilesDirName() ?? '']);

        $filterClass = $this->sourceType->getFilter();
        $filter = $filterClass && is_a($filterClass, Filter::class, true) ? new $filterClass() : null;

        while ($xml->read()) {
            if ($xml->nodeType !== $xml::ELEMENT || !in_array($xml->name, $keys, true)) {
                continue;
            }

            $lines++;

            if (!($lines % static::COUNT_FOR_FLUSH)) {
                $this->flushRows();
            }

            $el = new SimpleXMLElement($xml->readOuterXml());

            $columns = $mapper[$xml->name];

            if (!$columns) {
                continue;
            }

            $mapperDto->source = $el;

            foreach ($columns as $model => $fields) {
                $data = [];
                foreach ($fields as $fieldName => $propertyName) {
                    if (!is_string($propertyName) && is_callable($propertyName)) {
                        $mapperDto->result = $data;
                        $data[$fieldName] = call_user_func($propertyName, $mapperDto);
                        continue;
                    }

                    $extractor = StringValueExtractor::class;
                    $params = [];

                    if (is_array($propertyName) && !empty($propertyName['extractor'])) {
                        $extractor = $propertyName['extractor'];
                        $params = $propertyName['params'] ?? [];
                        $propertyName = $propertyName['name'] ?? '';
                    }

                    if (!property_exists($el, $propertyName)) {
                        $this->logger->error(
                            'Column "' . $propertyName . '" not found',
                            ['Model' => $model, 'Field' => $fieldName]
                        );
                        continue;
                    }

                    try {
                        $data[$fieldName] = call_user_func((new $extractor()), $el->{$propertyName}, ...$params);
                    } catch (Throwable $e) {
                        $this->logger->error(
                            'Value extractor error',
                            [
                                'column' => $propertyName,
                                'extractor' => $extractor,
                                'error' => $e->getMessage()
                            ]
                        );
                    }
                }

                if (!$this->filterModel($model, $data, $filter)) {
                    continue;
                }

                $this->addRow($model, $data);
            }
            unset($el);
        }
        $xml->close();

        unset($mapperDto, $xml);

        if ($this->rows) {
            $this->flushRows();
        }

        return $lines;
    }
}
