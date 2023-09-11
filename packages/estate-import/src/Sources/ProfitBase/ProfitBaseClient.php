<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\ProfitBase;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Kelnik\EstateImport\Providers\EstateImportServiceProvider;
use Kelnik\EstateImport\Sources\ProfitBase\Contracts\ProfitBaseClient as ClientInterface;
use Kelnik\EstateImport\Sources\ProfitBase\Contracts\ProfitBaseConfig;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @see https://developer.profitbase.ru/
 */
final class ProfitBaseClient implements ClientInterface
{
    private const API_VERSION = '4.0';

    private const TOKEN_LIFE_TIME = 86_400; // 24h to seconds
    private const REQUEST_TIME_THROTTLE = 1; // seconds
    private const PREMISES_PER_PAGE = 100;
    private const SUCCESS_RESPONSE = 'success';
    private const ERROR_RESPONSE = 'error';

    private const URL_AUTH = '/authentication';
    private const URL_COMPLEXES = '/projects';
    private const URL_BUILDINGS = '/house';
    private const URL_PREMISES = '/property';
    private const URL_STATUSES = '/custom-status/list';
    private const URL_PREMISE_TYPES = '/property-types';

    private ?string $token = null;
    private int $tokenRemaining = 0;
    private int $tokenCreatedAt = 0;
    private bool $checkToken = true;

    private int $lastQueryTime = 0;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ProfitBaseConfig $config
    ) {
        $this->getTokenData();
    }

    public function checkConnection(): bool
    {
        try {
            $this->getToken();
        } catch (Throwable $e) {
            $this->logger->error('Error on connection', ['msg' => $e->getMessage()]);

            return false;
        }

        return $this->token !== null;
    }

    public function getComplexes(array $params = []): array
    {
        return $this->request(self::URL_COMPLEXES, $params);
    }

    public function getBuildings(array $params = []): array
    {
        $response = $this->request(self::URL_BUILDINGS, $params);

        return (int)Arr::get($response, self::SUCCESS_RESPONSE, 0)
            ? Arr::get($response, 'data', [])
            : [];
    }

    public function getBuildingsByComplexId(int|array $id): array
    {
        return $this->getBuildings(is_array($id) ? ['projectIds' => $id] : ['projectId' => $id]);
    }

    public function getPremises(array $params = []): iterable
    {
        $page = 0;
        $hasMore = true;

        do {
            $params['offset'] = $page * self::PREMISES_PER_PAGE;
            $params['limit'] = self::PREMISES_PER_PAGE;

            $response = $this->request(self::URL_PREMISES, $params);
            $res = Arr::get($response, 'status') === self::SUCCESS_RESPONSE
                ? Arr::get($response, 'data', [])
                : [];

            if (!$res || count($res) < self::PREMISES_PER_PAGE) {
                $hasMore = false;
            }

            foreach ($res as $el) {
                yield $el;
            }

            $page++;
        } while ($hasMore);
    }

    public function getPremisesByBuildingId(int|array $id): iterable
    {
        return $this->getPremises(['houseId' => $id, 'full' => 'true']);
    }

    public function getStatuses(): array
    {
        $response = $this->request(self::URL_STATUSES, ['crm' => 'bitrix']);

        return (int)Arr::get($response, self::SUCCESS_RESPONSE)
            ? Arr::get($response, 'data.customStatuses', [])
            : [];
    }

    public function getPremiseTypes(): array
    {
        return $this->request(self::URL_PREMISE_TYPES);
    }

    /** @throws Exception */
    private function getToken(): void
    {
        $this->checkToken = false;
        $response = $this->request(
            self::URL_AUTH,
            [
                'type' => 'api-app',
                'credentials' => ['pb_api_key' => $this->config->apiKey]
            ],
            'POST'
        );

        $token = Arr::get($response, 'access_token');
        $this->checkToken = true;

        if (!$token) {
            throw new Exception('Token error: ' . Arr::get($response, self::ERROR_RESPONSE));
        }

        $this->token = $token;
        $this->tokenRemaining = (int)Arr::get($response, 'remaining_time', 0);
        $this->tokenCreatedAt = time();

        $this->saveTokenData();
    }

    private function tokenIsValid(): bool
    {
        return $this->token !== null && strlen($this->token) && $this->tokenTimeValid();
    }

    private function tokenTimeValid(): bool
    {
        return $this->tokenCreatedAt
            && (time() - $this->tokenCreatedAt) < ($this->tokenRemaining ?: self::TOKEN_LIFE_TIME);
    }

    private function saveTokenData(): void
    {
        Cache::tags([
            EstateImportServiceProvider::MODULE_NAME
        ])->put(
            $this->getCacheId(),
            [
                'token' => Crypt::encrypt($this->token),
                'remaining' => $this->tokenRemaining,
                'createdAt' => $this->tokenCreatedAt
            ],
            $this->tokenRemaining
        );
    }

    private function getTokenData(): void
    {
        $cache = Cache::get($this->getCacheId());

        if (!$cache || !strlen($cache['token'] ?? '')) {
            return;
        }

        $this->token = Crypt::decrypt($cache['token']);
        $this->tokenRemaining = (int)($cache['remaining'] ?? 0);
        $this->tokenCreatedAt = (int)($cache['createdAt'] ?? 0);
    }

    private function getCacheId(): string
    {
        return 'client_profitbase_data_' . md5($this->config->apiUrl . '|' . $this->config->apiKey);
    }

    private function request(string $url, array $data = [], string $method = 'GET'): array
    {
        $url = rtrim($this->config->apiUrl, '/') . $url;
        $logData = $data;

        $logData['credentials']['pb_api_key'] = '*****';

        $queryParams = [];

        if ($this->lastQueryTime && (time() - $this->lastQueryTime <= self::REQUEST_TIME_THROTTLE)) {
            sleep(1);
        }

        if ($this->checkToken && !$this->tokenIsValid()) {
            try {
                $this->logger->debug('Token request');
                $this->getToken();
            } catch (Throwable $e) {
                $this->logger->error('Request error', ['error' => $e->getMessage()]);

                return [];
            }
        }

        if ($this->token) {
            $queryParams['access_token'] = $this->token;
        }

        if ($method === 'GET') {
            $queryParams = array_merge($data, $queryParams);
            $url .= '?' . http_build_query($queryParams);
            $data = null;
            $logData = null;
        }

        $this->logger->debug(
            'Request for ' . str_replace('access_token=' . $this->token, 'access_token=*****', $url),
            [
                'client' => self::class,
                'method' => $method,
                'data' => $logData
            ]
        );

        try {
            $res = Http::retry(3, self::REQUEST_TIME_THROTTLE * 1_000 + 500)
                ->withoutVerifying()
                ->timeout($this->config->connectionTimeOut)
                ->acceptJson()
                ->withBody($data ? json_encode($data) : null, 'application/json')
                ->send($method, $url)
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

        return $res;
    }
}
