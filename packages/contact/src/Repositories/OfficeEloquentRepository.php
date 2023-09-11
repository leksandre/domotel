<?php

declare(strict_types=1);

namespace Kelnik\Contact\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\Contact\Models\Office;
use Kelnik\Contact\Repositories\Contracts\OfficeRepository;

final class OfficeEloquentRepository implements OfficeRepository
{
    /** @var class-string $model */
    private $model = Office::class;

    public function findByPrimary(int|string $primary): Office
    {
        return $this->model::findOrNew($primary);
    }

    public function getAll(): Collection
    {
        return $this->model::query()->orderBy('priority')->orderBy('title')->get();
    }

    public function getActive(): Collection
    {
        return $this->model::query()
            ->with('image')
            ->where('active', true)
            ->orderBy('priority')
            ->orderBy('title')
            ->get();
    }

    public function save(Model $model): bool
    {
        return $model->save();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
