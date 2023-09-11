<?php

declare(strict_types=1);

namespace Kelnik\News\Models;

final class ElementButton implements Contracts\ElementButton
{
    private string $link;
    private string $text;
    private string $target = '_self';

    public function __construct(string $link, string $text, ?string $target)
    {
        $this->link = $link;
        $this->text = $text;

        if (!is_null($target)) {
            $this->target = $target;
        }
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function toArray(): array
    {
        return ['link' => $this->link, 'text' => $this->text, 'target' => $this->target];
    }
}
