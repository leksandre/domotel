<?php

declare(strict_types=1);

namespace Kelnik\Core\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Kelnik\Core\Models\Contracts\SiteSettings;
use Kelnik\Core\Models\Site;

final class SiteSettingsCast implements CastsAttributes
{
    /**
     * @param Site $model
     * @param string $key
     * @param ?string $value
     * @param array $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes): SiteSettings
    {
        $value = $value ? json_decode($value, true) : [];

        if (!$value && !$model->exists) {
            $value['seo'] = config('kelnik-core.site.settings.seo');
        }

        return resolve(SiteSettings::class, ['data' => $value]);
    }

    /**
     * @param Site $model
     * @param string $key
     * @param SiteSettings $value
     * @param array $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return json_encode($value->toArray());
    }
}
