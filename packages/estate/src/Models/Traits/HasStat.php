<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Kelnik\Estate\Models\Stat;

trait HasStat
{
    public function stat(): MorphMany
    {
        return $this->morphMany(Stat::class, 'statAble', 'model_name', 'model_row_id');
    }
}
