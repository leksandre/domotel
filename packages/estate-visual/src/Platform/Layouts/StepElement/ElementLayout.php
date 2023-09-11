<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Layouts\StepElement;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class ElementLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Input::make('element.title')
                ->title('kelnik-estate-visual::admin.title')
                ->required()
        ];
    }
}
