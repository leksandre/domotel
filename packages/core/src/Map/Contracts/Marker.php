<?php

declare(strict_types=1);

namespace Kelnik\Core\Map\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface Marker extends Arrayable
{
    public function __construct(array $data);

    public function getId(): int;

    public function getType(): string;

    public function getTitle(): string;

    public function getCoords(): Coords;

    public function getIcon(): null|string|Icon;

    public function getBalloon(): ?Balloon;
}
