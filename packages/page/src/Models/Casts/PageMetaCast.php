<?php

declare(strict_types=1);

namespace Kelnik\Page\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Kelnik\Page\Models\Contracts\PageMeta;
use Kelnik\Page\Models\Page;

final class PageMetaCast implements CastsAttributes
{
    /**
     * @param Page $model
     * @param string $key
     * @param ?string $value
     * @param array $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes): PageMeta
    {
        return resolve(
            PageMeta::class,
            [
                'pageId' => $model->getKey() ?? 0,
                'data' => $value ? json_decode($value, true) : []
            ]
        );
    }

    /**
     * @param Page $model
     * @param string $key
     * @param PageMeta $value
     * @param array $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return json_encode($value->toArray());
    }
}
