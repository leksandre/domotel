<?php

declare(strict_types=1);

namespace Kelnik\Contact\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ScopeActive
{
    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }
}
