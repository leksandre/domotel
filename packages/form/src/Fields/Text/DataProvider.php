<?php

declare(strict_types=1);

namespace Kelnik\Form\Fields\Text;

use Kelnik\Form\Fields\PlatformDataProvider;

final class DataProvider extends PlatformDataProvider
{
    public function getEditLayouts(): array
    {
        return [
            DataLayout::class
        ];
    }
}
