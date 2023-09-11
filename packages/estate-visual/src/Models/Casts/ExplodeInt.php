<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Kelnik\Estate\Models\Contracts\EstateModel;

final class ExplodeInt implements CastsAttributes
{
    /**
     * @param EstateModel $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return array
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        return array_map('intval', explode(',', (string)$value));
    }

    /**
     * @param EstateModel $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return implode(',', (array)$value);
    }
}
