<?php

declare(strict_types=1);

namespace Kelnik\News\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Kelnik\News\Models\Contracts\ElementMeta;
use Kelnik\News\Models\Element;

final class ElementMetaCast implements CastsAttributes
{
    /**
     * @param Element $model
     * @param string $key
     * @param ?string $value
     * @param array $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes): ElementMeta
    {
        return resolve(
            ElementMeta::class,
            ['data' => $value ? json_decode($value, true) : []]
        );
    }

    /**
     * @param Element $model
     * @param string $key
     * @param ElementMeta $value
     * @param array $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return json_encode($value->toArray());
    }
}
