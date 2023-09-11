<?php

declare(strict_types=1);

namespace Kelnik\Progress\Database\Factories;

use Faker\Provider\Base;

final class VimeoFaker extends Base
{
    public function vimeoEmbedLink(): string
    {
        return 'https://player.vimeo.com/video/' . $this->vimeoId();
    }

    public function vimeoId(): string
    {
        $characters = '0123456789';
        $result = '';
        $maxCharNum = strlen($characters) - 1;
        $maxNumber = mt_rand(6, 8);

        for ($i = 0; $i <= $maxNumber; $i++) {
            $minValue = !$i ? 1 : 0;
            $result .= mt_rand($minValue, $maxCharNum);
        }

        return $this->generator->parse($result);
    }
}
