<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Allio;

use Closure;
use Exception;
use Illuminate\Support\Arr;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Models\Proxy\PremisesStatus;
use Kelnik\EstateImport\Models\Proxy\PremisesTypeGroup;
use Kelnik\EstateImport\PreProcessor\Contracts\Filter;
use Kelnik\EstateImport\PreProcessor\Contracts\Mapper;
use Kelnik\EstateImport\PreProcessor\Contracts\PreProcessor as AbstractPreProcessor;
use Kelnik\EstateImport\PreProcessor\IsNotUniqueException;
use Kelnik\EstateImport\PreProcessor\MapperDto;
use Kelnik\EstateImport\PreProcessor\Traits\FilterModel;
use Kelnik\EstateImport\Sources\Allio\Contracts\HasMultiValues;
use Kelnik\EstateImport\Sources\Allio\Mappers\Building;
use Kelnik\EstateImport\Sources\Allio\Mappers\Feature;
use Kelnik\EstateImport\Sources\Allio\Mappers\Premises;
use Kelnik\EstateImport\ValueExtractors\StringValueExtractor;
use Throwable;

final class PreProcessor extends AbstractPreProcessor
{
    use FilterModel;

    private const COUNT_FOR_FLUSH = 50;

    private \Kelnik\EstateImport\Sources\Contracts\SourceType $sourceType;

    public function __construct(History $history)
    {
        parent::__construct($history);

        $this->sourceType = new SourceType();
    }

    /**
     * @throws Throwable
     * @throws IsNotUniqueException
     */
    public function execute(): bool
    {
        $this->logger->info('Run pre-import', ['class' => self::class]);
        $this->history->setStateIsPreProcess();
        $this->history->setResultForState([
            'time' => ['start' => now()->getTimestamp()]
        ]);
        $this->historyRepository->save($this->history);

        if (config('kelnik-estate-import.unique.enable')) {
            $this->checkUnique();
        }

        $this->logger->info('Starting import from ProfitBase');

        try {
            $cnt = $this->importStaticData();
            $cnt += $this->getDataFromResource();

            if ($cnt === false) {
                throw new Exception('Empty data list');
            }
        } catch (Throwable $e) {
            $this->logger->error('Parsing error', ['error' => $e->__toString()]);
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

    private function importStaticData(): int
    {
        $data = [
            PremisesStatus::class => [
                [
                    'external_id' => 'FREE',
                    'title' => trans('kelnik-estate-import::allio.status.free')
                ],
                [
                    'external_id' => 'RESERVED',
                    'title' => trans('kelnik-estate-import::allio.status.reserved')
                ],
                [
                    'external_id' => 'SOLD',
                    'title' => trans('kelnik-estate-import::allio.status.sold')
                ]
            ],
            PremisesTypeGroup::class => [
                [
                    'external_id' => 'APARTMENT',
                    'living' => true,
                    'slug' => 'apartment',
                    'title' => trans('kelnik-estate-import::allio.typeGroup.apartment')
                ],
                [
                    'external_id' => 'PARKING',
                    'slug' => 'parking',
                    'title' => trans('kelnik-estate-import::allio.typeGroup.parking')
                ],
                [
                    'external_id' => 'COMMERCE',
                    'slug' => 'commerce',
                    'title' => trans('kelnik-estate-import::allio.typeGroup.commerce')
                ],
                [
                    'external_id' => 'STORAGE',
                    'slug' => 'storage',
                    'title' => trans('kelnik-estate-import::allio.typeGroup.storage')
                ]
            ]
        ];

        $lines = 0;

        foreach ($data as $model => $items) {
            foreach ($items as $item) {
                $this->addRow($model, $item);

                $lines++;

                if (!($lines % self::COUNT_FOR_FLUSH)) {
                    $this->flushRows();
                }
            }
        }

        return $lines;
    }

    /** @throws Exception */
    private function getDataFromResource(): bool|int
    {
        $params = $this->getParams();

        $paramsDto = new ParamsDto();
        $paramsDto->client = $this->sourceType->getClient($params, $this->logger);

        $paramsDto->mapperDto = new MapperDto();
        $paramsDto->mapperDto->logger = &$this->logger;
        $paramsDto->mapperDto->storage = &$this->storage;
        $paramsDto->mapperDto->historyDirPath = $this->getHistoryDirName() ?? '';
        $paramsDto->mapperDto->filesDirPath = $paramsDto->mapperDto->historyDirPath;

        $filterClass = $this->sourceType->getFilter();
        $paramsDto->filter = $filterClass && is_a($filterClass, Filter::class, true) ? new $filterClass() : null;

        return $this->processBlocks($paramsDto);
    }

    private function processBlocks(ParamsDto $params): int
    {
        $lines = 0;
        $buildings = [];

        $blocks = [
            [
                'src' => fn () => $params->client->getBuildings(),
                'mapper' => Building::class,
                'afterAdd' => function (string $modelClassName, array $modelData) use (&$buildings) {
                    if ($modelClassName !== \Kelnik\EstateImport\Models\Proxy\Building::class) {
                        return;
                    }
                    $buildings[] = $modelData['external_id'];
                }
            ],
            [
                'src' => function () use ($params, &$buildings) {
                    return $buildings ? $params->client->getPremisesByBuilding($buildings) : [];
                },
                'mapper' => [Feature::class, Premises::class]
            ]
        ];

        $replacement = (array)Arr::get(
            $this->importSettingsService->getSourceParams($this->sourceType),
            'replacement.list'
        );

        foreach ($blocks as $block) {
            $mappers = $block['mapper'];

            if (!is_array($mappers)) {
                $mappers = [$mappers];
            }

            $mapperObj = [];

            foreach ($block['src']() as $el) {
                foreach ($mappers as $mapperClassName) {

                    /** @psalm-var class-string<Mapper> $mapperClassName */

                    if (!isset($mapperObj[$mapperClassName])) {
                        $mapperObj[$mapperClassName] = new $mapperClassName($replacement);
                    }

                    $mapper = &$mapperObj[$mapperClassName];
                    $params->mapperDto->source = $el;

                    $lines++;

                    if (!($lines % self::COUNT_FOR_FLUSH)) {
                        $this->flushRows();
                    }

                    if ($mapper instanceof HasMultiValues) {
                        $this->processMultiRow($mapper, $params, $el);
                        continue;
                    }

                    $this->processRow($mapper(), $params, $el, $block['afterAdd'] ?? null);
                }
            }

            $this->flushRows();
        }

        return $lines;
    }

    private function processRow(array $mapper, ParamsDto $params, array $el, ?Closure $afterAddCallback = null): void
    {
        foreach ($mapper as $model => $fields) {
            $data = [];

            foreach ($fields as $fieldName => $propertyName) {
                if (!is_string($propertyName) && is_callable($propertyName)) {
                    $params->mapperDto->result = $data;
                    $data[$fieldName] = call_user_func($propertyName, $params->mapperDto);
                    continue;
                }

                $extractor = StringValueExtractor::class;
                $extractorParams = [];

                if (is_array($propertyName) && !empty($propertyName['extractor'])) {
                    $extractor = $propertyName['extractor'];
                    $extractorParams = $propertyName['params'] ?? [];
                    $propertyName = $propertyName['name'] ?? '';
                }

                if (!array_key_exists($propertyName, $el)) {
                    $this->logger->error(
                        'Column "' . $propertyName . '" not found',
                        ['Model' => $model, 'Field' => $fieldName]
                    );
                    continue;
                }

                try {
                    $data[$fieldName] = call_user_func((new $extractor()), $el[$propertyName], ...$extractorParams);
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

            if (!$this->filterModel($model, $data, $params->filter)) {
                continue;
            }

            $this->addRow($model, $data);

            if ($afterAddCallback) {
                call_user_func($afterAddCallback, $model, $data);
            }
        }
    }

    /**
     * @param Contracts\HasMultiValues & Mapper $mapper
     * @param ParamsDto $params
     * @param array $el
     * @return void
     */
    private function processMultiRow(HasMultiValues $mapper, ParamsDto $params, array $el): void
    {
        $items = $mapper->getElements($params->mapperDto);

        if (!$items) {
            return;
        }

        $mapper = $mapper();

        foreach ($items as $item) {
            $params->mapperDto->source = $item;
            $this->processRow($mapper, $params, $item);
        }
    }

    /** @throws Exception */
    private function getParams(): array
    {
        $params = $this->importSettingsService->getSourceParams($this->sourceType);

        if (
            !filter_var($params['client']['url'] ?? '', FILTER_VALIDATE_URL)
            || empty($params['client']['login'])
            || empty($params['client']['password'])
            || empty($params['client']['developer'])
        ) {
            $this->logger->error('Authentication params is empty');
            throw new Exception(trans('kelnik-estate-import.admin'));
        }

        return $params;
    }
}
