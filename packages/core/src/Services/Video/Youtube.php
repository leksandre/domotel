<?php

declare(strict_types=1);

namespace Kelnik\Core\Services\Video;

use Kelnik\Core\Services\Contracts\VideoService;

final class Youtube extends VideoService
{
    private const PLAYER_BASE_URL = 'https://www.youtube.com/embed/';

    public static function canUse(string $url): bool
    {
        return (bool) preg_match('!^(https?://)?(www\.)?((youtu\.be|youtube\.com)/[a-z0-9]+).*$!i', $url);
    }


    public function getName(): string
    {
        return 'youtube';
    }

    public function getPlayerLink(): ?string
    {
        $videoId = $this->getVideoId();

        return $videoId
            ? self::PLAYER_BASE_URL . $videoId
            : null;
    }

    public function getLoopPlayerLink(): ?string
    {
        $videoId = $this->getVideoId();

        return $videoId
            ? self::PLAYER_BASE_URL . $videoId . '?rel=0&modestbranding=1&autohide=1&showinfo=0' .
                '&controls=0&autoplay=1&loop=1&mute=1&playlist=' . $videoId . '&enablejsapi=1'
            : null;
    }

    public function getThumb(): ?string
    {
        $videoId = $this->getVideoId();

        return $videoId
            ? 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg'
            : null;
    }
}
