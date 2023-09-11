<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Fields;

use Kelnik\Core\Theme\Contracts\Color;
use Orchid\Screen\Field;

/**
 * @method ColorPicker title(string $value = null)
 * @method ColorPicker label(string $value = null)
 * @method ColorPicker default(string $value = null)
 * @method ColorPicker help(string $value = null)
 */
class ColorPicker extends Field
{
    protected $view = 'kelnik-core::platform.fields.colorpicker';

    protected $inlineAttributes = [
        'title',
        'name',
        'label'
    ];

    public function __construct()
    {
        $this->addBeforeRender(
            function () {
                $color = $this->get('value');
                if ($color instanceof Color) {
                    if (!isset($this->attributes['data-default'])) {
                        $this->set('data-default', $color->getDefaultValue());
                    }
                    $color = $color->getValue();
                }
                $this->set('data-color', $color ?? $this->get('data-default'));
            }
        );
    }
}
