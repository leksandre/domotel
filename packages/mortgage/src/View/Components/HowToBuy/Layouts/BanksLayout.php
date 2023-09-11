<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\View\Components\HowToBuy\Layouts;

use Closure;
use Kelnik\Mortgage\Models\Bank;
use Kelnik\Mortgage\Platform\Services\Contracts\MortgagePlatformService;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class BanksLayout extends Rows
{
    public function __construct(private Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        return [
//            Relation::make('data.banks.id.')
//                ->title('kelnik-mortgage::admin.components.howToBuy.banks')
//                ->fromModel(Bank::class, 'title', 'id')
//                ->multiple()
//                ->help('kelnik-mortgage::admin.components.howToBuy.banksHelp'),
            Switcher::make('data.banks.showRange')
                ->title('kelnik-mortgage::admin.components.howToBuy.banksShowRange')
                ->help('kelnik-mortgage::admin.components.howToBuy.banksShowRangeHelp')
                ->sendTrueOrFalse(),

            resolve(MortgagePlatformService::class)->getContentLink(),

            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
