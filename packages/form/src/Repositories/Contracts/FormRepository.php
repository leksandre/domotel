<?php

declare(strict_types=1);

namespace Kelnik\Form\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kelnik\Form\Models\Form;

interface FormRepository extends BaseRepository
{
    public function findByPrimary(int|string $primary): Form;

    public function findActiveByPrimary(int|string $primary): Form;

    public function isUnique(Form $form): bool;

    public function getAll(): Collection;

    public function getAdminListPaginated(): LengthAwarePaginator;

    public function save(Model $model, ?array $emails = null): bool;
}
