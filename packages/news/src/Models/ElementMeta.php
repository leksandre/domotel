<?php

declare(strict_types=1);

namespace Kelnik\News\Models;

use Illuminate\Support\Arr;

final class ElementMeta implements Contracts\ElementMeta
{
    private const TITLE = 'title';
    private const DESCRIPTION = 'description';
    private const KEYWORDS = 'keywords';

    private array $data = [];

    public function __construct(array $data = [])
    {
        $this->fill($data ?? []);
    }

    public function fill(array $data): void
    {
        $this->data = Arr::only(
            $data,
            [self::TITLE, self::DESCRIPTION, self::KEYWORDS]
        );
    }

    public function setTitle(?string $value): void
    {
        $this->data[self::TITLE] = $value;
    }

    public function getTitle(): ?string
    {
        return $this->data[self::TITLE] ?? null;
    }

    public function setDescription(?string $value): void
    {
        $this->data[self::DESCRIPTION] = $value;
    }

    public function getDescription(): ?string
    {
        return $this->data[self::DESCRIPTION] ?? null;
    }

    public function setKeywords(?string $value): void
    {
        $this->data[self::KEYWORDS] = $value;
    }

    public function getKeywords(): ?string
    {
        return $this->data[self::KEYWORDS] ?? null;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
