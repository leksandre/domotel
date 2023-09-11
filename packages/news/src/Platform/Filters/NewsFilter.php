<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Filters;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Kelnik\News\Models\Category;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class NewsFilter extends Filter
{
    public $parameters = [
        'title',
        'category'
    ];

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
        $category = (array)$this->request->get('category');
        $category = array_map('intval', $category);
        $category = array_filter($category);

        if ($title) {
            $builder->where('title', 'like', '%' . $this->request->get('title') . '%');
        }

        if ($category) {
            $builder->whereIn('category_id', $category);
        }

        return $builder;
    }

    /**
     * @return array|Field[]
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function display(): array
    {
        return [
            Input::make('title')
                ->placeholder('kelnik-news::admin.filter.fieldTitle')
                ->value($this->request->get('title')),
            Relation::make('category')
                ->fromModel(Category::class, 'title')
                ->placeholder('kelnik-news::admin.filter.fieldCategory')
                ->multiple()
                ->value($this->request->get('category'))
                ->allowEmpty()
        ];
    }
}
