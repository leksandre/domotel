<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\ProfitBase\Mappers;

use Kelnik\EstateImport\ValueExtractors\StringValueExtractor;

trait FeatureTrait
{
    /** @var string[] */
    private array $allowSingleProperties = [
        'is_free_layout',
        'is_euro_layout'
    ];

    /**
     * @var string[]
     * Также особенности с префиксом `pbcf_`
     */
    private array $allowGroupProperties = [
        'facing',
        'window',
        'loggia_count',
        'balcony_count',
        'combined_bathroom_count',
        'separated_bathroom_count'
    ];

    private function getFeatureExternalId(array $propertyData): string
    {
        return $this->replaceExternalId(
            $this->isSingleFeature($propertyData['id'])
                ? mb_strtolower($propertyData['id'])
                : $propertyData['id'] . '__' . mb_strtolower((new StringValueExtractor())($propertyData['value'] ?? ''))
        );
    }

    private function isSingleFeature(string $name): bool
    {
        return in_array($name, $this->allowSingleProperties);
    }

    private function isGroupFeature(string $name): bool
    {
        return $this->isClientField($name) || in_array($name, $this->allowGroupProperties);
    }

    private function isClientField(string $name): bool
    {
        return str_starts_with($name, 'pbcf_');
    }

    private function isAllowedFeature(string $name): bool
    {
        return $this->isGroupFeature($name) || $this->isSingleFeature($name);
    }
}
