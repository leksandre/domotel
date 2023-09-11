<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\PremisesCard\Layouts;

use Closure;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class VrLayout extends Rows
{
    public function __construct(private readonly Fieldable|Groupable|Closure $tabFooter)
    {
//        $this->title(trans('kelnik-estate::admin.components.premisesCard.vr.title'));
    }

    protected function fields(): array
    {
        return [
            Switcher::make('data.vr.active')
                ->title('kelnik-estate::admin.components.premisesCard.vr.active')
                ->sendTrueOrFalse(),
            Input::make('data.vr.buttonText')
                ->title('kelnik-estate::admin.components.premisesCard.vr.buttonText'),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
