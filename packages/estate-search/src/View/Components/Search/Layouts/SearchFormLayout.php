<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\View\Components\Search\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\EstateSearch\View\Components\Search\DataProvider;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

final class SearchFormLayout extends Rows
{
    public function __construct(private readonly Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        return [
            Title::make('')->value(
                trans('kelnik-estate-search::admin.components.search.data.filters')
            ),
            Matrix::make('data.filters')
                //->title('kelnik-estate-search::admin.components.search.data.filters')
                ->sortable(true)
                ->columns([
                    trans('kelnik-estate-search::admin.components.search.data.filter') => 'class',
                    trans('kelnik-estate-search::admin.components.search.data.title') => 'title',
                ])
                ->fields([
                    'class' => Select::make()
                        ->options($this->query->get('filters'))
                        ->empty(trans('kelnik-estate-search::admin.noValue'), DataProvider::NO_VALUE),
                    'title' => Input::make()
                ])
                ->help('kelnik-estate-search::admin.components.search.data.filtersHelp'),
            Title::make('')->value(
                trans('kelnik-estate-search::admin.components.search.data.orders')
            ),
            Matrix::make('data.orders')
//                ->title('kelnik-estate-search::admin.components.search.data.orders')
                ->sortable(true)
                ->columns([
                    trans('kelnik-estate-search::admin.components.search.data.order') => 'class',
                    trans('kelnik-estate-search::admin.components.search.data.titleAsc') => 'titleAsc',
                    trans('kelnik-estate-search::admin.components.search.data.titleDesc') => 'titleDesc'
                ])
                ->fields([
                    'class' => Select::make()
                        ->options($this->query->get('orders'))
                        ->empty(trans('kelnik-estate-search::admin.noValue'), DataProvider::NO_VALUE),
                    'titleAsc' => Input::make(),
                    'titleDesc' => Input::make()
                ]),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
