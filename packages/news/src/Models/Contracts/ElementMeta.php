<?php

declare(strict_types=1);

namespace Kelnik\News\Models\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface ElementMeta extends Arrayable
{
    public function __construct(array $data = []);

    public function fill(array $data): void;

    public function setTitle(?string $value): void;

    public function getTitle(): ?string;

    public function setDescription(?string $value): void;

    public function getDescription(): ?string;

    public function setKeywords(?string $value): void;

    public function getKeywords(): ?string;
}
