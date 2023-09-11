<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\Filters;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Kelnik\EstateVisual\Models\Filters\Contracts\AbstractFilter;

final class FilterResourceFactory
{
    private static array $types = [
        AbstractFilter::TYPE_CHECKBOX => CheckboxResource::class,
        AbstractFilter::TYPE_SLIDER => SliderResource::class
    ];

    public static function make(string $type, Collection $filter, ?Collection $data = null): JsonResource
    {
        if (!$type || !isset(self::$types[$type])) {
            throw new InvalidArgumentException('Type ' . $type . ' has no resource class');
        }

        $className = self::$types[$type];

        return new $className($filter, $data);
    }
}
