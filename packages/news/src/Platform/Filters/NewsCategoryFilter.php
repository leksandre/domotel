<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class NewsCategoryFilter extends Filter
{
    public $parameters = ['title'];

    public function name(): string
    {
        return trans('kelnik-news::admin.filter.title');
    }

    /**
     * @param Builder $builder
     * @return Builder
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(Builder $builder): Builder
    {
        $title = $this->request->get('title');

        if ($title) {
            $builder->where('title', 'like', '%' . $this->request->get('title') . '%');
        }

        return $builder;
    }

    /**
     * @return array|Field[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function display(): array
    {
        return [
            Input::make('title')
                ->placeholder('kelnik-news::admin.filter.fieldTitle')
                ->value($this->request->get('title'))
        ];
    }
}
