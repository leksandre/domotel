<?php

declare(strict_types=1);

namespace Kelnik\News\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Kelnik\News\Models\Contracts\ElementButton;
use Kelnik\News\Models\Element;

final class ElementButtonCast implements CastsAttributes
{
    /**
     * @param Element $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return ElementButton
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ElementButton
    {
        $value = $value === null ? [] : json_decode($value, true);

        return resolve(
            ElementButton::class,
            [
                'link' => $value['link'] ?? '',
                'text' => $value['text'] ?? '',
                'target' => $value['target'] ?? null
            ]
        );
    }

    /**
     * @param Element $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof ElementButton) {
            $value = $value->toArray();
        }

        return json_encode($value);
    }
}
