<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Fields;

use Illuminate\Support\Collection;
use Orchid\Screen\Fields\RadioButtons;

/**
 * @method TemplateSelector options(Collection $value)
 */
final class TemplateSelector extends RadioButtons
{
    protected $view = 'kelnik-page::platform.fields.templateSelector';
}
