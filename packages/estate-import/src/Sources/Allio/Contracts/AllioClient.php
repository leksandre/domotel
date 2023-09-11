<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Allio\Contracts;

use Kelnik\EstateImport\Sources\Contracts\ClientBase;
use Psr\Log\LoggerInterface;

interface AllioClient extends ClientBase
{
    public function __construct(LoggerInterface $logger, AllioConfig $config);

    public function getDevelopers(): array;

    public function getBuildings(): array;

    /**
     * @param int|int[] $buildingId
     * @return iterable
     */
    public function getPremisesByBuilding(int|array $buildingId): iterable;
}
