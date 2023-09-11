<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Repositories\Estate;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\PremisesTypeGroupRepository;

final class PremisesTypeGroupEloquentRepository implements PremisesTypeGroupRepository
{
    protected string $modelNamespace = PremisesTypeGroup::class;

    public function getListWithTypes(): Collection
    {
        return $this->modelNamespace::select(['id', 'living', 'build_title', 'title'])
            ->with([
                'types' => fn(HasMany $builder) => $builder
                    ->select('id', 'group_id', 'title')
                    ->orderBy('priority')
                    ->orderBy('title')
            ])
            ->orderBy('priority')
            ->orderBy('title')
            ->get();
    }
}
