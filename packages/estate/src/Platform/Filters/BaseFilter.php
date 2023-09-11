<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;

abstract class BaseFilter extends Filter
{
    public $parameters = [
        'title'
    ];

    public function name(): string
    {
        return trans('kelnik-estate::admin.filter.title');
    }

    public function run(Builder $builder): Builder
    {
        $id = $this->request->integer('id');
        $title = $this->request->get('title', '');

        if ($id > 0) {
            $builder->where('id', '=', $id);
        }

        if (strlen($title)) {
            $title = '%' . $title . '%';
            $builder->where(fn(Builder $builder) => $builder->where('title', 'like', $title));
        }

        return $builder;
    }

    /** @return Field[] */
    public function display(): array
    {
        return [
            Input::make('id')
                ->placeholder('kelnik-estate::admin.filter.fieldId')
                ->value($this->request->get('id')),
            Input::make('title')
                ->placeholder('kelnik-estate::admin.filter.fieldTitle')
                ->value($this->request->get('title'))
        ];
    }
}
