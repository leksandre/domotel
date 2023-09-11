<?php

declare(strict_types=1);

namespace Kelnik\Core\Services\Video;

use Kelnik\Core\Services\Contracts\VideoService;

final class Vimeo extends VideoService
{
    private const PLAYER_BASE_URL = 'https://player.vimeo.com/video/';

    public static function canUse(string $url): bool
    {
        return (bool) preg_match('!^(https?://)?(www\.|player\.)?(vimeo\.com/(video/)?\d+).*$!i', $url);
    }

    public function getName(): string
    {
        return 'vimeo';
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
            ? self::PLAYER_BASE_URL . $videoId . '?autoplay=1&loop=1&muted=1&controls=0&title=0'
            : null;
    }

    public function getThumb(): ?string
    {
        $videoId = $this->getVideoId();

        return $videoId
            ? 'https://vumbnail.com/' . $videoId . '.jpg'
            : null;
    }
}
