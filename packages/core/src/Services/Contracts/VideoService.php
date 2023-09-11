<?php

declare(strict_types=1);

namespace Kelnik\Core\Services\Contracts;

abstract class VideoService
{
    public function __construct(protected readonly string $url)
    {
    }

    abstract public static function canUse(string $url): bool;

    abstract public function getName(): string;

    abstract public function getPlayerLink(): ?string;

    abstract public function getLoopPlayerLink(): ?string;

    abstract public function getThumb(): ?string;

    public function getVideoId(): string
    {
        $urlParts = parse_url($this->url);
        $query = [];
        parse_str($urlParts['query'] ?? '', $query);

        $videoId = explode('/', trim($urlParts['path'] ?? '', '/'));

        return end($videoId);
    }
}
