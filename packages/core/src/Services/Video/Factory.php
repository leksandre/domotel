<?php

declare(strict_types=1);

namespace Kelnik\Core\Services\Video;

use Kelnik\Core\Services\Contracts\VideoService;

final class Factory
{
    private static array $services = [
        Vimeo::class,
        Youtube::class
    ];

    public static function make(string $url): ?VideoService
    {
        /** @var VideoService $videoService */
        foreach (self::$services as $videoService) {
            if ($videoService::canUse($url)) {
                return new $videoService($url);
            }
        }

        return null;
    }
}
