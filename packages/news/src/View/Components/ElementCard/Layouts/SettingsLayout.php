<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\ElementCard\Layouts;

use Kelnik\Core\Platform\Fields\Title;
use Kelnik\News\Platform\Services\Contracts\NewsPlatformService;
use Kelnik\News\View\Components\ElementCard\ElementCard;
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
                ->title('kelnik-news::admin.components.elementCard.data.title'),
            Switcher::make('active')
                ->title('kelnik-news::admin.active')
                ->sendTrueOrFalse(),
            Select::make('data.template')
                ->title('kelnik-news::admin.components.elementCard.template')
                ->options(ElementCard::getTemplates()->pluck('title', 'name')->toArray())
                ->required()
                ->hr(),
            Title::make('dfg')->value(trans('kelnik-news::admin.components.elementCard.data.otherHeader')),
            Input::make('data.other.title')
                ->title('kelnik-news::admin.components.elementCard.data.otherTitle'),
            Input::make('data.other.count')
                ->title('kelnik-news::admin.components.elementCard.data.otherCount')
                ->type('number')
                ->min(0)
                ->max(10)
                ->addBeforeRender(function () {
                    if ($this->get('value') === null) {
                        $this->set('value', config('kelnik-news.card.otherElementsCount'));
                    }
                }),
            resolve(NewsPlatformService::class)->getContentLink()
        ];
    }
}
