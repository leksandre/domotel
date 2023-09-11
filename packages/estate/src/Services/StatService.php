<?php

declare(strict_types=1);

namespace Kelnik\Estate\Services;

use Exception;
use Kelnik\Estate\Jobs\ClearingModuleCache;
use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\Section;
use Kelnik\Estate\Repositories\Contracts\PremisesStatusRepository;
use Kelnik\Estate\Repositories\Contracts\StatRepository;

final class StatService implements Contracts\StatService
{
    public function __construct(private StatRepository $statRepository)
    {
    }

    public function run(): bool
    {
        try {
            $this->updateStat($this->getObjectsData());
            ClearingModuleCache::dispatch();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    private function getModels(): array
    {
        return [
            'complex' => Complex::class,
            'building' => Building::class,
            'section' => Section::class,
            'floor' => Floor::class
        ];
    }

    public function updateStat(array $data): bool
    {
        if (!$data) {
            return false;
        }

        $this->statRepository->updateStat($this->getModels(), $data);

        return true;
    }

    public function getObjectsData(int|string $complexId = ''): array
    {
        return $this->statRepository->getObjectsStat(
            $complexId,
            resolve(PremisesStatusRepository::class)->getListForStat()->pluck('id')->toArray(),
            array_keys($this->getModels())
        );
    }

    public function getEdgePrices(): array
    {
        $prices = $this->statRepository->getEdgePrices([Complex::class]);
        $res = [];

        if (!$prices) {
            return $res;
        }

        foreach ($prices as $price) {
            $method = $price['name'] === 'price_min' ? 'min' : 'max';
            $res[$price['name']] = call_user_func($method, $res[$price['name']] ?? $price['value'], $price['value']);
        }

        return $res;
    }
}
