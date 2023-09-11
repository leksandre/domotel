<?php

declare(strict_types=1);

namespace Kelnik\Core\Theme\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Stringable;

interface Color extends Arrayable, JsonSerializable, Stringable
{
    public function __construct(
        string $name,
        null|string $value = null,
        null|string $defaultValue = null,
        null|string $title = null
    );

    public function getName(): string;

    public function getFullName(): string;

    public function getCssName(): string;

    public function getCssValue(): ?string;

    public function getTitle(): ?string;

    public function getValue(): mixed;

    public function setValue(string $value): void;

    public function getDefaultValue(): ?string;

    public function setDefaultValue(string $defaultValue): void;

    public function isDifferentFromDefault(): bool;
}
