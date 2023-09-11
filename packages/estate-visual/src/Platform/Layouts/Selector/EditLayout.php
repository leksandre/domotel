<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Layouts\Selector;

use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Estate\Models\Complex;
use Kelnik\EstateVisual\Models\Steps\Factory;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class EditLayout extends Rows
{
    protected function fields(): array
    {
        $selector = $this->query->get('selector');
        $exists = $selector?->exists ?? false;
        $fields = [
            Input::make('selector.title')
                ->title('kelnik-estate-visual::admin.title')
                ->required(),
            Switcher::make('selector.active')
                ->title('kelnik-estate-visual::admin.active')
                ->sendTrueOrFalse()
                ->hr(),
            Title::make('')->value(trans('kelnik-estate-visual::admin.selectorRef')),
            Relation::make('selector.complex_id')
                ->title('kelnik-estate-visual::admin.complex')
                ->fromModel(Complex::class, 'title')
                ->applyScope('adminList')
                ->required(!$exists)
                ->disabled($exists)
        ];

        $showSteps = false;

        if ($selector && !$selector->exists) {
            $showSteps = true;
        }

        if ($showSteps) {
            $fields[] = Title::make('')->value(trans('kelnik-estate-visual::admin.createSteps'));
            foreach (Factory::STEP_TO_CLASS as $name => $className) {
                $step = Factory::make($name, $selector);
                $fields[] = Switcher::make('selector.steps.' . $name)->placeholder($step->getTitle());
            }
        }

        return $fields;
    }
}
