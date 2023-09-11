<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateImport\Sources;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Kelnik\EstateImport\PreProcessor\MapperDto;
use Kelnik\EstateImport\Services\Contracts\DownloadService;
use Kelnik\Tests\TestCase;
use Mockery\MockInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractMapper extends TestCase
{
    protected array $cache = [];
    protected LoggerInterface $logger;
    protected Filesystem $storage;
    protected string $dirPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->storage = Storage::fake();
        $this->dirPath = 'history_' . md5($this->mapperClassName);

        $this->app->bind(
            DownloadService::class,
            fn() => $this->partialMock(
                DownloadService::class,
                function (MockInterface $mock) {
                    $mock->shouldReceive('download')
                        ->andReturnUsing(function ($arg) {
                            $fileInfo = pathinfo(
                                parse_url(
                                    func_get_arg(0) ?: '',
                                    PHP_URL_PATH
                                )
                            );

                            return [
                                'hash' => $fileInfo['filename'] ?? '',
                                'path' => $fileInfo['basename'] ?? ''
                            ];
                        });
                }
            )
        );
    }

    /**
     * @var string
     * @psam-var class-string
     */
    protected string $mapperClassName;

    protected function rowExists(string $name, array $data): bool
    {
        return isset($this->cache[$name . '_' . ($data['external_id'] ?? '')]);
    }

    protected function cacheRow(string $name, array $data): void
    {
        $this->cache[$name . '_' . ($data['external_id'] ?? '')] = 1;
    }

    /** @dataProvider mapperProvider */
    public function testParsingRowIsCorrect(array $row, array $res)
    {
        $mapper = (new $this->mapperClassName())();
        $mapperDto = new MapperDto();
        $mapperDto->source = $row;
        $mapperDto->logger = $this->logger;
        $mapperDto->storage = $this->storage;
        $mapperDto->historyDirPath = $this->dirPath;
        $mapperDto->filesDirPath = null;

        $resRow = [];

        foreach ($mapper as $model => $fields) {
            $data = [];
            foreach ($fields as $fieldName => $column) {
                if (!is_string($column) && is_callable($column)) {
                    $mapperDto->result = $data;
                    $data[$fieldName] = call_user_func($column, $mapperDto);
                    continue;
                }

                if (array_key_exists($column, $row)) {
                    $data[$fieldName] = $row[$column];
                }
            }

            $tmpData = $data;
            unset($tmpData['hash']);

            if (!array_filter($tmpData) || $this->rowExists($model, $data)) {
                continue;
            }

            $resRow[$model] = $data;
            $this->cacheRow($model, $data);
        }
        unset($mapper, $mapperDto);

        $this->assertEquals($resRow, $res);
    }

    abstract public static function mapperProvider(): array;
}
