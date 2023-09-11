<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Fields;

use Orchid\Screen\Concerns\Multipliable;
use Orchid\Screen\Fields\Select;

final class SelectNative extends Select
{
    use Multipliable;

    protected $view = 'kelnik-core::platform.fields.select-native';

    public function __construct()
    {
        parent::__construct();

        $this->set('style', 'height:auto !important');
    }
}
