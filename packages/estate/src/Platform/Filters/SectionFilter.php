<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Filters;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Kelnik\Estate\Models\Building;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Relation;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SectionFilter extends BaseFilter
{
    public $parameters = [
        'id',
        'title',
        'building'
    ];

    public function run(Builder $builder): Builder
    {
        $builder = parent::run($builder);

        $building = (array)$this->request->get('building');
        $building = array_map('intval', $building);
        $building = array_filter($building);

        if ($building) {
            $builder->whereIn('building_id', $building);
        }

        return $builder;
    }

    /**
     * @return Field[]
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function display(): array
    {
        return array_merge(
            parent::display(),
            [
                Relation::make('building')
                    ->fromModel(Building::class, 'title')
                    ->placeholder('kelnik-estate::admin.filter.fieldBuilding')
                    ->applyScope('adminList')
                    ->displayAppend('admin_title')
                    ->multiple()
                    ->allowEmpty()
                    ->value($this->request->get('building'))
            ]
        );
    }
}
