<?php

declare(strict_types=1);

namespace Kelnik\Core\Theme;

final class Color implements Contracts\Color
{
    private string $name;
    private ?string $title;
    private string $suffix = '-rgb';
    private ?string $value;
    private ?string $defaultValue = null;
    private bool $isRgb = false;

    public function __construct(
        string $name,
        null|string $value = null,
        null|string $defaultValue = null,
        null|string $title = null
    ) {
        if ($value === null) {
            $value = '';
        }

        $this->value = strtolower(trim($value));
        $this->name = strtolower(trim($name));

        if (stripos($this->name, $this->suffix)) {
            $this->name = str_replace($this->suffix, '', $this->name);
            $this->isRgb = true;
        }

        if ($defaultValue) {
            $this->defaultValue = $defaultValue;
        }

        if ($title) {
            $this->title = $title;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFullName(): string
    {
        return $this->name . ($this->isRgb ? $this->suffix : '');
    }

    public function getCssName(): string
    {
        return '--color-' . $this->getFullName();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getCssValue(): string
    {
        return $this->isRgb ? $this->getValueRGB() : $this->getValue();
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(string $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    public function setValue(string $value): void
    {
        $this->value = strtolower(trim($value));
    }

    public function getValueRGB(): string
    {
        $value = ltrim($this->value, '#');
        $value = str_split($value, 2);
        $value = array_map('hexdec', $value);

        return implode(',', $value);
    }

    public function isDifferentFromDefault(): bool
    {
        return $this->defaultValue && $this->value !== $this->defaultValue;
    }

    public function toArray(): array
    {
        return [$this->getFullName() => $this->getValue()];
    }

    public function jsonSerialize()
    {
        return json_encode($this->toArray());
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
