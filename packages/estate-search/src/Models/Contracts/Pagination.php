<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Contracts;

use Kelnik\EstateSearch\Models\Enums\PaginationType;
use Kelnik\EstateSearch\Models\Enums\PaginationViewType;

abstract class Pagination
{
    public function __construct(
        public readonly PaginationType $type,
        public readonly PaginationViewType $viewType,
        public readonly int $perPage
    ) {
    }
}
