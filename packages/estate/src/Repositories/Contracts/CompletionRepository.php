<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Kelnik\Estate\Models\Completion;

interface CompletionRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): Completion;
}
