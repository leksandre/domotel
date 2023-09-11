<?php

declare(strict_types=1);

namespace Kelnik\Core\Map;

final class Balloon implements Contracts\Balloon
{
    private readonly ?string $imageUrl;
    private readonly ?string $title;
    private readonly ?string $text;

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'image' => $this->imageUrl,
            'text' => $this->text
        ];
    }

    public function __construct(string $title, string $text, ?string $imageUrl = null)
    {
        $this->title = $title;
        $this->text = $text;
        $this->imageUrl = $imageUrl;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }
}
