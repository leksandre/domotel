<?php

declare(strict_types=1);

namespace Kelnik\Contact\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\Contact\Models\SocialLink;
use Kelnik\Contact\Repositories\Contracts\SocialLinkRepository;

final class SocialLinkEloquentRepository implements SocialLinkRepository
{
    /** @var class-string $model */
    private $model = SocialLink::class;

    public function findByPrimary(int|string $primary): SocialLink
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
            ->with('icon')
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
