<?php

declare(strict_types=1);

namespace Kelnik\Page\Repositories\Contracts;

use Illuminate\Support\Collection;

interface BladeComponentRepository
{
    public function getViewComponents(): Collection;

    public function getDynamicComponents(): Collection;

    /**
     * List of "code => value"
     *
     * @return Collection
     */
    public function getAdminList(): Collection;

    /**
     * Full list of components
     * @return Collection
     */
    public function getList(): Collection;

    /**
     * Find component by primary key (id or code)
     *
     * @param int|string $primary
     *
     * @return mixed
     */
    public function findByPrimary(int|string $primary): mixed;
}
