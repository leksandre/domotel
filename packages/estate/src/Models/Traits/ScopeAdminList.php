<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ScopeAdminList
{
    public function scopeAdminList(Builder $query): Builder
    {
        return $query->orderBy('priority')->orderBy('title');
    }
}
