<?php

declare(strict_types=1);

namespace Kelnik\Core\Models\Enums\Contracts;

interface HasTitle
{
    public function title(): ?string;
}
