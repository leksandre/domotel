<?php

declare(strict_types=1);

namespace Kelnik\Page\Models\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface BufferDto extends Arrayable
{
    public function getCacheTags(): array;

    public function getCardRoutes(): array;
}
