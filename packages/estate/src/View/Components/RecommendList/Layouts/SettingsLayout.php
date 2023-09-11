<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\RecommendList\Layouts;

use Kelnik\Estate\Platform\Services\Contracts\EstatePlatformService;
use Kelnik\Estate\View\Components\RecommendList\RecommendList;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class SettingsLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Input::make('data.title')
                ->title('kelnik-estate::admin.components.recommendList.data.title'),
            Switcher::make('active')
                ->title('kelnik-estate::admin.active')
                ->sendTrueOrFalse(),
            Input::make('data.count')
                ->type('number')
                ->min($this->query->get('min'))
                ->max($this->query->get('max'))
                ->value($this->query->get('default'))
                ->title('kelnik-estate::admin.components.recommendList.data.count'),
            Select::make('data.template')
                ->title('kelnik-estate::admin.components.recommendList.data.template')
                ->options(RecommendList::getTemplates()->pluck('title', 'name')->toArray())
                ->required(),
            resolve(EstatePlatformService::class)->getContentLink()
        ];
    }
}
