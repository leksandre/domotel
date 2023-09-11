<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\RecommendList\Contracts;

abstract class Filter
{
    public int|string $typeGroupKey = 0;
    public int|string $typeKey = 0;
    public int|string $excludeKey = 0;
    public int $floorKey = 0;
    public int $floorNum = 0;
    public float $priceTotal = 0;
    public float $areaTotal = 0;
    public array $features = [];
    public int $limit = 0;
}
