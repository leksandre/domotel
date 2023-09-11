<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\PremisesCard\Layouts;

use Closure;
use Kelnik\Estate\Platform\Services\Contracts\EstatePlatformService;
use Kelnik\Estate\View\Components\PremisesCard\PremisesCard;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
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
        return [
            Input::make('data.title')
                ->title('kelnik-estate::admin.components.premisesCard.data.title'),
            Switcher::make('active')
                ->title('kelnik-estate::admin.active')
                ->sendTrueOrFalse(),
            Select::make('data.template')
                ->title('kelnik-estate::admin.components.premisesCard.template')
                ->options(PremisesCard::getTemplates()->pluck('title', 'name')->toArray())
                ->required(),
            Select::make('data.background')
                ->title('kelnik-estate::admin.components.premisesCard.background')
                ->options(PremisesCard::getBackgroundVariants())
                ->hr(),
            resolve(EstatePlatformService::class)->getContentLink(),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
