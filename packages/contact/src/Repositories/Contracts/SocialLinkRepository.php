<?php

declare(strict_types=1);

namespace Kelnik\Contact\Repositories\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Contact\Models\SocialLink;

interface SocialLinkRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): SocialLink;

    public function getAll(): Collection;
}
