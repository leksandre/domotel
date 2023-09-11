<?php

declare(strict_types=1);

namespace Kelnik\Page\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Kelnik\Core\Theme\Color;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Page\View\Components\Contracts\NullComponent;

final class ComponentDataProviderCast implements CastsAttributes
{
    /**
     * @param PageComponent $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     *
     * @return ComponentDataProvider
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ComponentDataProvider
    {
        if (
            !is_string($model->component)
            || !strlen($model->component)
            || !class_exists($model->component)
            || !is_a($model->component, KelnikPageComponent::class, true)
        ) {
            return (
            new class extends NullComponent {
            })::initDataProvider();
        }

        $value = $value === null ? [] : json_decode($value, true);

        if (!empty($value['theme']['colors'])) {
            foreach ($value['theme']['colors'] as $name => &$val) {
                $val = new Color(name: $name, value: $val);
            }
        }

        $data = collect($value);

        $dataProvider = $model->component::initDataProvider();
        $dataProvider->setValue($data);

        return $dataProvider;
    }

    /**
     * @param Page $model
     * @param string $key
     * @param string|ComponentDataProvider $value
     * @param array $attributes
     *
     * @return string
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if ($value instanceof ComponentDataProvider) {
            $value = $value->toArray();
        }

        return json_encode($value);
    }
}
