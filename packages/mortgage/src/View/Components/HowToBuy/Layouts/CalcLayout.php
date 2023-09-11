<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\View\Components\HowToBuy\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Core\Platform\Fields\Range;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Form\Platform\Services\Contracts\FormPlatformService;
use Kelnik\Mortgage\View\Components\Contracts\BaseMortgageCalc;
use Kelnik\Mortgage\View\Components\HowToBuy\DataProvider;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

final class CalcLayout extends Rows
{
    public function __construct(private Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        $coreService = $this->query->get('coreService');
        $number = Input::make('data.calc.base.minAmount')
            ->type('number')
            ->min(BaseMortgageCalc::MIN_VALUE)
            ->value(BaseMortgageCalc::MIN_VALUE)
            ->title('kelnik-mortgage::admin.components.howToBuy.calc.minAmount');

        $range = Range::make('data.calc.base.firstPayment')
            ->value(BaseMortgageCalc::MIN_VALUE)
            ->min((int)BaseMortgageCalc::MIN_FIRST_PAYMENT_PERCENT)
            ->max((int)BaseMortgageCalc::MAX_FIRST_PAYMENT_PERCENT)
            ->title('kelnik-mortgage::admin.components.howToBuy.calc.firstPayment');

        $res = [
            Title::make('')->value(trans('kelnik-mortgage::admin.components.howToBuy.calc.titleBase')),

            $number,

            (clone $number)
                ->set('name', 'data.calc.base.meanAmount')
                ->title('kelnik-mortgage::admin.components.howToBuy.calc.meanPrice'),

            (clone $number)
                ->set('name', 'data.calc.base.maxAmount')
                ->title('kelnik-mortgage::admin.components.howToBuy.calc.maxPrice'),

            $range,

            (clone $range)
                ->set('name', 'data.calc.base.meanTime')
                ->min(BaseMortgageCalc::MIN_VALUE)
                ->max(BaseMortgageCalc::MAX_TIME)
                ->title('kelnik-mortgage::admin.components.howToBuy.calc.meanTime'),

            Title::make('')
                ->help('kelnik-mortgage::admin.components.howToBuy.calc.help')
                ->hr(),

            Title::make('')->value(trans('kelnik-mortgage::admin.components.howToBuy.calc.titleCard')),

            (clone $range)
                ->set('name', 'data.calc.card.firstPayment')
                ->title('kelnik-mortgage::admin.components.howToBuy.calc.firstPayment'),

            (clone $range)
                ->set('name', 'data.calc.card.meanTime')
                ->min(BaseMortgageCalc::MIN_VALUE)
                ->max(BaseMortgageCalc::MAX_TIME)
                ->title('kelnik-mortgage::admin.components.howToBuy.calc.meanTime')
                ->hr(),

            Title::make('')->value(trans('kelnik-mortgage::admin.components.howToBuy.calc.titleOther')),

            Input::make('data.calc.phone')
                ->title('kelnik-mortgage::admin.components.howToBuy.calc.phone')
                ->maxlength(DataProvider::PHONE_LIMIT)
                ->mask(['regex' => '[0-9()\-+ ]+']),

            Input::make('data.calc.schedule')
                ->title('kelnik-mortgage::admin.components.howToBuy.calc.schedule')
                ->maxlength(DataProvider::SCHEDULE_LIMIT),

            Quill::make('data.calc.text')
                ->title('kelnik-mortgage::admin.components.howToBuy.calc.text')
                ->help(trans(
                    'kelnik-mortgage::admin.components.howToBuy.calc.button.textHelp',
                    ['limit' => DataProvider::CALC_TEXT_LIMIT]
                )),

            Quill::make('data.calc.helpText')
                ->title('kelnik-mortgage::admin.components.howToBuy.calc.helpText')
                ->help(trans(
                    'kelnik-mortgage::admin.components.howToBuy.calc.button.textHelp',
                    ['limit' => DataProvider::CALC_HELP_TEXT_LIMIT]
                ))->hr()
        ];

        if ($coreService->hasModule('form')) {
            /** @var FormPlatformService $formService */
            $formService = resolve(FormPlatformService::class);

            $res[] = Title::make('')->value(
                trans('kelnik-mortgage::admin.components.howToBuy.calc.button.header.callback')
            );

            $text = Input::make('data.calc.buttons.consult.text')
                ->title('kelnik-mortgage::admin.components.howToBuy.button.text')
                ->help(trans(
                    'kelnik-mortgage::admin.components.howToBuy.calc.button.textHelp',
                    ['limit' => DataProvider::BUTTON_TEXT_LIMIT]
                ))
                ->maxlength(DataProvider::BUTTON_TEXT_LIMIT);

            $select = Select::make('data.calc.buttons.consult.form_id')
                ->title('kelnik-mortgage::admin.components.howToBuy.button.form')
                ->options($formService->getList())
                ->empty(trans('kelnik-mortgage::admin.components.howToBuy.noValue'), DataProvider::NO_VALUE);

            $res[] = $text;
            $res[] = $select;

            $res[] = Title::make('')->value(
                trans('kelnik-mortgage::admin.components.howToBuy.calc.button.header.mortgage')
            );

            $res[] = (clone $text)->set('name', 'data.calc.buttons.mortgage.text');
            $res[] = (clone $select)->set('name', 'data.calc.buttons.mortgage.form_id');

            $res[] = $formService->getContentLink();
        }

        $res[] = is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter;

        return $res;
    }
}
