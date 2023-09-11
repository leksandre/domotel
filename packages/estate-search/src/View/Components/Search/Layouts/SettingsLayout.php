<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\View\Components\Search\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Estate\Repositories\Contracts\PremisesStatusRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeGroupRepository;
use Kelnik\EstateSearch\Models\Contracts\SearchConfig;
use Kelnik\EstateSearch\Models\Enums\PaginationType;
use Kelnik\EstateSearch\Models\Enums\PaginationViewType;
use Kelnik\EstateSearch\View\Components\Search\Search;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\RadioButtons;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class SettingsLayout extends Rows
{
    public function __construct(private readonly Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        $typeGroupRepo = resolve(PremisesTypeGroupRepository::class);
        $statusRepo = resolve(PremisesStatusRepository::class);

        $setValue = function ($obj, $enumClass): void {
            $value = $obj->get('value');

            if ($value) {
                return;
            }

            $obj->set('value', $enumClass::getDefault()->value);
        };

        return [
            Input::make('data.title')
                ->title('kelnik-estate-search::admin.components.search.data.title'),
            Switcher::make('active')
                ->title('kelnik-estate-search::admin.active')
                ->sendTrueOrFalse(),
            Select::make('data.types')
                ->title('kelnik-estate-search::admin.components.search.data.types')
                ->options($typeGroupRepo->getAll()->pluck('title', 'id')->toArray())
                ->multiple(),
            Select::make('data.statuses')
                ->title('kelnik-estate-search::admin.components.search.data.statuses')
                ->options($statusRepo->getAll()->pluck('title', 'id')->toArray())
                ->multiple()
                ->hr(),
            Title::make('')->value(
                trans('kelnik-estate-search::admin.components.search.data.design.title')
            ),
            Select::make('data.template')
                ->title('kelnik-estate-search::admin.components.search.data.design.template')
                ->options(Search::getTemplates()->pluck('title', 'name')->toArray())
                ->required(),
            RadioButtons::make('data.view')
                ->title('kelnik-estate-search::admin.components.search.data.defaultView')
                ->options(Search::getViewTypes()->toArray())
                ->required(),
            Switcher::make('data.switch')
                ->title('kelnik-estate-search::admin.components.search.data.switch')
                ->sendTrueOrFalse()
                ->hr(),
            Title::make('')->value(
                trans('kelnik-estate-search::admin.components.search.data.pagination.title')
            ),
            RadioButtons::make('data.pagination.type')
                ->title('kelnik-estate-search::admin.components.search.data.pagination.type')
                ->options($this->query->get('paginationTypes'))
                ->addBeforeRender(function () use ($setValue) {
                    $setValue($this, PaginationType::class);
                }),
            Select::make('data.pagination.viewType')
                ->title('kelnik-estate-search::admin.components.search.data.pagination.viewType')
                ->options($this->query->get('paginationViewTypes'))
                ->addBeforeRender(function () use ($setValue) {
                    $setValue($this, PaginationViewType::class);
                }),
            Input::make('data.pagination.perPage')
                ->type('number')
                ->title('kelnik-estate-search::admin.components.search.data.pagination.perPage')
                ->min(SearchConfig::PAGINATION_PER_PAGE_MIN)
                ->max(SearchConfig::PAGINATION_PER_PAGE_MAX)
                ->value(SearchConfig::PAGINATION_PER_PAGE_DEFAULT)
                ->required()
                ->hr(),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
