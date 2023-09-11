<?php

declare(strict_types=1);

namespace Kelnik\Page\Models;

use Kelnik\Page\Models\Contracts\BufferDto;

final class PageStatusBufferDto implements BufferDto
{
    public int $status = 0;

    public function toArray(): array
    {
        return [
            'status' => $this->status
        ];
    }

    public function getCacheTags(): array
    {
        return [];
    }

    public function getCardRoutes(): array
    {
        return [];
    }
}
