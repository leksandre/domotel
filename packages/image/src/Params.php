<?php

declare(strict_types=1);

namespace Kelnik\Image;

use Kelnik\Image\Contracts\AbstractParams;
use Kelnik\Image\Contracts\ImageFile;

final class Params extends AbstractParams
{
    public function __construct(?ImageFile $image = null)
    {
        if ($image) {
            $this->filename = $image->getFullName();
        }
    }
}
