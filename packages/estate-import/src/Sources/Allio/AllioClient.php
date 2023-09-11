<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Allio;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Kelnik\EstateImport\Sources\Allio\Contracts\AllioClient as ClientInterface;
use Kelnik\EstateImport\Sources\Allio\Contracts\AllioConfig;
use Psr\Log\LoggerInterface;
use Throwable;

final class AllioClient implements ClientInterface
{
    private const API_VERSION = '1.0';

    private const REQUEST_TIME_THROTTLE = 1; // seconds

    private const METHOD_DEVELOPERS = 'bugh1c/developers/list';
    private const METHOD_BUILDINGS = 'bugh1c/buildings/list';
    private const METHOD_PREMISES_TYPES = 'bugh1c/spacetypes/list';
    private const METHOD_PREMISES_LIST = 'bugh1c/spaces/list';
    private const METHOD_PREMISES_CARD = 'space/get';

    private int $lastQueryTime = 0;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly AllioConfig $config
    ) {
    }

    public function checkConnection(): bool
    {
        foreach ($this->getDevelopers() as $el) {
            if (($el['id'] ?? null) === $this->config->apiDeveloper) {
                return true;
            }
        }

        return false;
    }

    public function getDevelopers(): array
    {
        return $this->request(self::METHOD_DEVELOPERS);
    }

    public function getBuildings(): array
    {
        return $this->request(self::METHOD_BUILDINGS, ['developer_id' => $this->config->apiDeveloper]);
    }

    public function getPremisesByBuilding(int|array $buildingId): iterable
    {
        if (!is_array($buildingId)) {
            $buildingId = [$buildingId];
        }

        foreach ($buildingId as $building) {
            $types = $this->getPremisesTypesByBuilding($building);

            foreach ($types as $type) {
                $premises = $this->request(
                    self::METHOD_PREMISES_LIST,
                    [
                        'building_id' => $building,
                        'stype_id' => $type['id']
                    ]
                );

                foreach ($premises as $el) {
                    yield $this->getPremisesData($el['id']);
                }
            }
        }

        return [];
    }

    private function getPremisesTypesByBuilding(int $buildingId): array
    {
        return $this->request(self::METHOD_PREMISES_TYPES, ['building_id' => $buildingId]);
    }

    private function getPremisesData(int $id): array
    {
        return $this->request(self::METHOD_PREMISES_CARD, ['id' => $id]);
    }

    private function request(string $path, array $params = []): array
    {
        $apiUrl = rtrim($this->config->apiUrl, '/');

        if ($this->lastQueryTime && (time() - $this->lastQueryTime <= self::REQUEST_TIME_THROTTLE)) {
            sleep(self::REQUEST_TIME_THROTTLE);
        }

        $data = [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => $path,
            'params' => $params
        ];

        $this->logger->debug('Request for ' . $apiUrl, $data);

        try {
            $res = Http::retry(3, 500)
                ->withBasicAuth($this->config->apiLogin, $this->config->apiPassword)
                ->withoutVerifying()
                ->timeout($this->config->connectionTimeOut)
                ->acceptJson()
                ->withBody(json_encode($data), 'application/json')
                ->send('POST', $apiUrl)
                ->json();
        } catch (Throwable $e) {
            $this->logger->error('Request error', ['msg' => $e->getMessage()]);
            return [];
        }

        if (!is_array($res)) {
            $res = [];
        }

        if (!empty($res['error'])) {
            $this->logger->error('Request error', ['client' => self::class, ...$res]);
            $res = [];
        }

        return Arr::get($res, 'result', []);
    }
}
