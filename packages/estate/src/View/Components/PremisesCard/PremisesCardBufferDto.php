<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\PremisesCard;

use Kelnik\Page\Models\Contracts\BufferDto;

final class PremisesCardBufferDto implements BufferDto
{
    public array $cacheTags = [];
    public array $cardRoutes = [];

    public int $elementId = 0;
    public float $priceTotal = 0.0;
    public float $areaTotal = 0.0;
    public array $features = [];

    public int $typeGroupId = 0;
    public int $typeId = 0;

    public int $floorId = 0;
    public int $floorNum = 0;
    public int $sectionId = 0;
    public int $buildingId = 0;
    public int $complexId = 0;

    public function getCacheTags(): array
    {
        return $this->cacheTags;
    }

    public function getCardRoutes(): array
    {
        return $this->cardRoutes;
    }

    public function toArray(): array
    {
        $res = [];

        foreach (get_class_vars(self::class) as $propName => $defaultValue) {
            $res[$propName] = $this->{$propName};
        }

        return $res;
    }
}
