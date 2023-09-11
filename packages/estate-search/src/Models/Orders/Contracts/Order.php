<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Orders\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

interface Order extends Arrayable
{
    public function getName(): string;

    public function getTitle(?string $direction): ?string;

    public function setTitle(?string $titleAsc = null, ?string $titleDesc = null): void;

    public function getAdminTitle(): string;

    public function getDirection(): string;

    public function setRequestValues(array $values): void;

    public function getDataOrder(): Collection;

    public function isSelected(): bool;

    public function isSelectedWithDirection(string $direction): bool;

    public function setIsDefault(bool $value): void;

    public function isDefault(): bool;

    public function toArray(): array;
}
