<?php

declare(strict_types=1);

namespace Kelnik\Core\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Repositories\Contracts\BaseEloquentRepository;
use Orchid\Attachment\Models\Attachment;

final class AttachmentEloquentRepository extends BaseEloquentRepository implements AttachmentRepository
{
    protected $model = Attachment::class;

    public function findByPrimary(int|string $primary): Attachment
    {
        return $this->model::findOrNew($primary);
    }

    public function findByName(string $name): Attachment
    {
        return $this->model::where('name', $name)->firstOrNew();
    }

    public function findByNameAndExtension(string $name, string $extension): Attachment
    {
        return $this->model::where('name', $name)->where('extension', $extension)->firstOrNew();
    }

    public function findByHashAndGroup(string $hash, string $group): Attachment
    {
        return $this->model::where('hash', $hash)->where('group', $group)->firstOrNew();
    }

    public function getByPrimary(iterable $primary): Collection
    {
        return $this->model::whereKey($primary)->orderBy('sort')->orderBy('id')->get();
    }

    public function getByGroupName(string $groupName): LazyCollection
    {
        return Attachment::query()->where('group', $groupName)->cursor();
    }

    public function delete(Attachment $attachment): ?bool
    {
        return $attachment->delete();
    }

    public function deleteMass(array $keys): void
    {
        if (!$keys) {
            return;
        }

        $this->model::whereKey($keys)->get()?->each(static fn(Attachment $el) => $el->delete());
    }
}
