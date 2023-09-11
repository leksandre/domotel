<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\FBlock\Models\FlatBlock;
use Kelnik\FBlock\Repositories\Contracts\BlockRepository;

final class BlockEloquentRepository implements BlockRepository
{
    /** @var class-string $model */
    private string $model = FlatBlock::class;

    public function findByPrimary(int|string $primary): FlatBlock
    {
        return $this->model::findOrNew($primary);
    }

    public function findActiveByPrimary(int|string $primary, ?array $fields = null): FlatBlock
    {
        $res = $this->model::query()
            ->whereKey($primary)
            ->with(['images']);

        if ($fields) {
            $res->select($fields);
        }

        return $res->firstOrNew();
    }

    public function getActiveList(int $limit = 0, int $offset = 0): Collection
    {
        $query = $this->model::query()
            ->with('images')
            ->where('active', true)
            ->orderBy('priority')
            ->orderBy('id');

        if ($offset) {
            $query->offset($offset);
        }

        return $limit
            ? $query->limit($limit)->get()
            : $query->get();
    }

    public function getAll(): Collection
    {
        return $this->model::query()->orderBy('priority')->orderBy('title')->get();
    }

    /**
     * @param FlatBlock $model
     * @param array $imageIds
     *
     * @return bool
     */
    public function save(Model $model, array $imageIds = []): bool
    {
        $res = $model->save();

        if ($res) {
            $model->images()->sync($imageIds);
            $model->touch();
        }

        return $res;
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
