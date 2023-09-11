<?php

declare(strict_types=1);

namespace Kelnik\Page\Models\Contracts;

trait DeleteAttach
{
    private static array $deleteAttachIds = [];

    protected function getDeleteAttachIds(): array
    {
        return self::$deleteAttachIds;
    }
}
