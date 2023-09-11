<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Kelnik\FBlock\Models\Button;

final class ButtonCast implements CastsAttributes
{
    /**
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return Button
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): Button
    {
        $value = $value === null ? [] : json_decode($value, true);

        return new Button(
            $value['formKey'] ?? '',
            $value['text'] ?? '',
            $value['target'] ?? null
        );
    }

    /**
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof \Kelnik\FBlock\Models\Contracts\Button) {
            $value = $value->toArray();
        }

        return json_encode($value);
    }
}
