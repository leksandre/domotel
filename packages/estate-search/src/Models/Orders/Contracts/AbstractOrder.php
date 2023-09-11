<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Orders\Contracts;

use Illuminate\Support\Arr;

abstract class AbstractOrder implements Order
{
    protected const NAME = '';
    public const PARAM_NAME = 'sort';
    public const PARAM_DIRECTION = 'order';
    public const DIRECTION_ASC = 'asc';
    public const DIRECTION_DESC = 'desc';
    protected const TITLE_ASC = '';
    protected const TITLE_DESC = '';
    protected const ADMIN_TITLE = '';

    protected array $requestValues = [];
    protected ?string $titleAsc = null;
    protected ?string $titleDesc = null;
    protected bool $isDefault = false;

    public function getName(): string
    {
        return static::NAME;
    }

    public function getTitle(?string $direction = null): ?string
    {
        $direction = $direction ?: $this->getDirectionFromRequest();

        return $direction === static::DIRECTION_DESC
            ? $this->getTitleDesc()
            : $this->getTitleAsc();
    }

    public function setTitle(?string $titleAsc = null, ?string $titleDesc = null): void
    {
        $this->titleAsc = $titleAsc;
        $this->titleDesc = $titleDesc;
    }

    protected function getTitleAsc(): string
    {
        return $this->titleAsc ?? (static::TITLE_ASC ? trans(static::TITLE_ASC) : '');
    }

    protected function getTitleDesc(): string
    {
        return $this->titleDesc ?? (static::TITLE_DESC ? trans(static::TITLE_DESC) : '');
    }

    public function getAdminTitle(): string
    {
        return static::ADMIN_TITLE ? trans(static::ADMIN_TITLE) : '';
    }

    public function setRequestValues(array $values): void
    {
        $this->requestValues = $values;
    }

    public function getDirection(): string
    {
        return $this->getDirectionFromRequest();
    }

    public function isSelected(): bool
    {
        return $this->isSelectedByDefault() || $this->isSelectedByParams();
    }

    public function isSelectedWithDirection(string $direction): bool
    {
        return $this->isSelected() && $direction === $this->getDirectionFromRequest();
    }

    private function isSelectedByDefault(): bool
    {
        return $this->isDefault() && !$this->getOrderFromRequest();
    }

    private function isSelectedByParams(): bool
    {
        return $this->getOrderFromRequest() === static::NAME;
    }

    public function setIsDefault(bool $value): void
    {
        $this->isDefault = $value;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    protected function getOrderFromRequest(): ?string
    {
        return trim((string)Arr::get($this->requestValues, static::PARAM_NAME));
    }

    protected function getDirectionFromRequest(): string
    {
        return trim((string)Arr::get($this->requestValues, static::PARAM_DIRECTION)) === static::DIRECTION_DESC
            ? static::DIRECTION_DESC
            : static::DIRECTION_ASC;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'direction' => $this->getDirection(),
            'title' => $this->getTitle()
        ];
    }
}
