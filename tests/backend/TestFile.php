<?php

declare(strict_types=1);

namespace Kelnik\Tests;

use Illuminate\Contracts\Filesystem\Filesystem;
use Orchid\Attachment\File;

final class TestFile extends File
{
    public function setStorage(Filesystem $storage)
    {
        $this->storage = $storage;
    }
}
