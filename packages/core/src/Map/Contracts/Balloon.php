<?php

declare(strict_types=1);

namespace Kelnik\Core\Map\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface Balloon extends Arrayable
{
    public function __construct(string $title, string $text, ?string $imageUrl = null);

    public function getTitle(): string;

    public function getText(): string;

    public function getImageUrl(): ?string;
}
