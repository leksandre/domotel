<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Location\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Page\Platform\Fields\MatrixMarkerTypes;
use Kelnik\Page\Providers\PageServiceProvider;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class MapMarkerTypesLayout extends Rows
{
    public function __construct(private Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        return [
            MatrixMarkerTypes::make('data.map.markerTypes')
                ->title('kelnik-page::admin.components.location.marker.typesHeader')
                ->sortable(true)
                ->maxRows(50)
                ->columns([
                    trans('kelnik-page::admin.components.location.marker.icon') => 'icon',
                    trans('kelnik-page::admin.components.location.marker.title') => 'title'
                ])
                ->fields([
                    'icon' => Picture::make()
                        ->targetId()
                        ->class('matrix_picture')
                        ->groups(PageServiceProvider::MODULE_NAME),
                    'title' => Input::make()
                        ->maxlength(80)
                        ->set('data-hidden-field', 'code')
                        ->required()
                ])
                ->help('kelnik-page::admin.components.location.marker.helpTypes'),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
