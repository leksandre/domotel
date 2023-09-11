<?php

declare(strict_types=1);

namespace Kelnik\Progress\Repositories\Contracts;

use Illuminate\Support\Collection;

interface GroupRepository extends BaseRepository
{
    public function getAdminList(): Collection;

    public function getActive(): Collection;
}
