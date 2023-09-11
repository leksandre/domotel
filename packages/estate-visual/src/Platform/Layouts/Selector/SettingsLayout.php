<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Layouts\Selector;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Core\Platform\Fields\ColorPicker;
use Kelnik\Core\Platform\Fields\MatrixStatic;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Estate\Models\PremisesStatus;
use Orchid\Screen\Layouts\Rows;

final class SettingsLayout extends Rows
{
    public function __construct()
    {
        $this->title(trans('kelnik-estate-visual::admin.visual.colors'));
    }

    protected function fields(): array
    {
        /**
         * @var Collection $statuses
         * @var Collection $typeGroups
         */
        $statuses = $this->query->get('statuses');
        $typeGroups = $this->query->get('typeGroups');
        $defColor = $this->query->get('defColor');
        $grayColor = $this->query->get('grayColor');
        $colorValues = $this->query->get('selector')?->settings?->get('colors') ?? [];

        $fields = [
            0 => Title::make('title')->class('m-0 p-1')->style('font-weight: normal; font-size: 1rem')
        ];
        $columns = ['' => 0];
        $res = [];

        /** @var PremisesStatus $status */
        foreach ($statuses as $status) {
            $fieldName = $status->getKey();
            $color = $status->card_available ? $defColor : $grayColor;
            $columns[$status->title] = $fieldName;
            $fields[$fieldName] = ColorPicker::make('color')->set('data-default', $color);
        }

        $matrix = MatrixStatic::make('selector.settings_.colors')
            ->fields($fields)
            ->columns($columns);

        foreach ($typeGroups as $group) {
            $matrixCopy = clone $matrix;
            $values = [];
            foreach ($group->types as $type) {
                $row = [0 => $type->title];
                foreach ($columns as $title => $name) {
                    if (!$title) {
                        continue;
                    }

                    $color = $statuses->first(
                        static fn(PremisesStatus $status) => $status->getKey() === $name
                    )?->card_available ? $defColor : $grayColor;

                    $row[$name] = Arr::get($colorValues, $type->getKey() . '.' . $name, $color);
                }
                $values[$type->getKey()] = $row;
            }
            $res[] = $matrixCopy->title($group->title)->value($values);
        }
        unset($matrix);

        return $res;
    }
}
