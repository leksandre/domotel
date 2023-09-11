<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Proxy\Contracts;

interface BelongsToAttachment
{
    public function attachmentsArr(): array;
}
