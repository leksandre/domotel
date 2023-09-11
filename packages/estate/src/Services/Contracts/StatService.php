<?php

declare(strict_types=1);

namespace Kelnik\Estate\Services\Contracts;

interface StatService
{
    public function run(): bool;

    public function updateStat(array $data): bool;

    public function getObjectsData(int|string $complexId = ''): array;

    public function getEdgePrices(): array;
}
