<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories;

use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Completion;
use Kelnik\Estate\Repositories\Contracts\CompletionRepository;

final class CompletionEloquentRepository extends EstateEloquentRepository implements CompletionRepository
{
    protected string $modelNamespace = Completion::class;

    public function findByPrimary(int|string $primary): Completion
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getAll(array $fields = []): Collection
    {
        return $this->modelNamespace::adminList()->orderByDesc('event_date')->get();
    }
}
