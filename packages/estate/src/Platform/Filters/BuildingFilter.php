<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Filters;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Kelnik\Estate\Models\Complex;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Relation;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class BuildingFilter extends BaseFilter
{
    public $parameters = [
        'title',
        'complex'
    ];

    public function run(Builder $builder): Builder
    {
        $builder = parent::run($builder);

        $complex = (array)$this->request->get('complex');
        $complex = array_map('intval', $complex);
        $complex = array_filter($complex);

        if ($complex) {
            $builder->whereIn('complex_id', $complex);
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
                Relation::make('complex')
                    ->fromModel(Complex::class, 'title')
                    ->placeholder('kelnik-estate::admin.filter.fieldComplex')
                    ->applyScope('adminList')
                    ->multiple()
                    ->allowEmpty()
                    ->value($this->request->get('complex'))
            ]
        );
    }
}
