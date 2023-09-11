<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\HidePrice;

trait HasHiddenPrices
{
    public function hidePrices(): MorphMany
    {
        return $this->morphMany(HidePrice::class, 'hidePriceAble', 'model_type', 'model_row_id');
    }

    public static function getHidePrice(array $values): Collection
    {
        if ($values) {
            $values = array_map(
                function ($row) {
                    return ['premises_type_id' => $row];
                },
                $values
            );
        }

        return collect($values);
    }
}
