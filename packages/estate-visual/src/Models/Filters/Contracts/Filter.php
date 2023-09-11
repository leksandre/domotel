<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Filters\Contracts;

use Illuminate\Support\Collection;

interface Filter
{
    public function __construct(?Collection $selectorSettings = null);

    public function getType(): string;

    public function getName(): string;

    public function getTitle(): ?string;

    public function isHidden(): bool;

    public function getResult(Collection $dataFilter): ?Collection;

    public function setRequestValues(array $values): void;

    public function setAdditionalValues(array $values): void;

    public function setExcludeParams(array $params): void;

    public function getDataFilterParams(): Collection;
}
