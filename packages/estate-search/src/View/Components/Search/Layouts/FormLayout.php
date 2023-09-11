<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\View\Components\Search\Layouts;

use Closure;
use Kelnik\EstateSearch\View\Components\Search\DataProvider;
use Kelnik\Form\Platform\Services\Contracts\FormPlatformService;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

final class FormLayout extends Rows
{
    protected const BUTTON_TEXT_LIMIT = 100;
    protected const NO_VALUE = '0';

    public function __construct(private readonly Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        /** @var FormPlatformService $formPlatformService */
        $formPlatformService = resolve(FormPlatformService::class);

        return [
            Select::make('data.form_id')
                ->title('kelnik-estate-search::admin.components.search.data.form.element')
                ->options($formPlatformService->getList())
                ->empty(
                    trans('kelnik-estate-search::admin.components.search.data.form.noValue'),
                    DataProvider::NO_VALUE
                )
                ->help('kelnik-estate-search::admin.components.search.data.form.help'),

            $formPlatformService->getContentLink(),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
