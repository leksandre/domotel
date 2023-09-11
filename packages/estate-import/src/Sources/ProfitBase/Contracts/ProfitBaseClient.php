<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\ProfitBase\Contracts;

use Kelnik\EstateImport\Sources\Contracts\ClientBase;
use Psr\Log\LoggerInterface;

interface ProfitBaseClient extends ClientBase
{
    public function __construct(LoggerInterface $logger, ProfitBaseConfig $config);

    public function getComplexes(array $params = []): array;

    public function getBuildings(array $params = []): array;

    /**
     * @param int|int[] $id
     * @return array
     */
    public function getBuildingsByComplexId(int|array $id): array;

    public function getPremises(array $params = []): iterable;

    /**
     * @param int|int[] $id
     * @return array
     */
    public function getPremisesByBuildingId(int|array $id): iterable;

    public function getStatuses(): array;

    public function getPremiseTypes(): array;
}
