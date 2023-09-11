<?php

declare(strict_types=1);

namespace Kelnik\Contact\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Kelnik\Contact\Models\Office;
use Kelnik\Core\Map\Contracts\Coords;

final class CoordsCast implements CastsAttributes
{
    /**
     * @param Office $model
     * @param string $key
     * @param ?string $value
     * @param array $attributes
     * @return Coords
     */
    public function get($model, string $key, $value, array $attributes): Coords
    {
        $value = $value ? json_decode($value, true) : [];
        $value = array_map('floatval', $value);

        return resolve(Coords::class, [
            'lat' => $value[0] ?? Coords::DEFAULT_COORDS,
            'lng' => $value[1] ?? Coords::DEFAULT_COORDS
        ]);
    }

    /**
     * @param Office $model
     * @param string $key
     * @param null|array|Coords $value
     * @param array $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        $res = [0, 0];

        if ($value instanceof Coords) {
            $res = $value->toArray();
        } elseif (is_array($value)) {
            $res = array_map('floatval', array_values($value));
        }

        return json_encode($res);
    }
}
