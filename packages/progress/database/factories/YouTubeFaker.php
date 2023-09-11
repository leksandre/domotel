<?php

declare(strict_types=1);

namespace Kelnik\Progress\Database\Factories;

use Faker\Provider\Base;

final class YouTubeFaker extends Base
{
    public function youTubeEmbedLink(): string
    {
        return 'https://youtube.com/embed/' . $this->youTubeId();
    }

    public function youTubeId(): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
        $result = '';
        $max = mb_strlen($characters) - 1;

        for ($i = 0; $i < 11; $i++) {
            $result .= $characters[mt_rand(0, $max)];
        }

        return $this->generator->parse($result);
    }
}
