<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\EstateVisual\Models\Contracts\Position;

final class PositionCast implements CastsAttributes
{
    /**
     * @param EstateModel $model
     * @param string $key
     * @param ?string $value
     * @param array $attributes
     * @return Position
     */
    public function get($model, string $key, $value, array $attributes): Position
    {
        $value = json_decode($value ?? '[0, 0]', true);

        if (!is_array($value) || !$value) {
            $value = [0, 0];
        }

        return resolve(
            Position::class,
            [
                'left' => $value[0] ?? 0,
                'top' => $value[1] ?? 0
            ]
        );
    }

    /**
     * @param EstateModel $model
     * @param string $key
     * @param ?Position $value
     * @param array $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return json_encode($value instanceof Position ? $value->toArray() : [0, 0]);
    }
}
