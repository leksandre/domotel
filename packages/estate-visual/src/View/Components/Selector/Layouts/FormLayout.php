<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\View\Components\Selector\Layouts;

use Kelnik\Core\Platform\Fields\Title;
use Kelnik\EstateVisual\View\Components\Selector\DataProvider;
use Kelnik\Form\Platform\Services\Contracts\FormPlatformService;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

final class FormLayout extends Rows
{
    protected function fields(): array
    {
        /** @var FormPlatformService $formPlatformService */
        $formPlatformService = resolve(FormPlatformService::class);

        return [
            Title::make('')->value(trans('kelnik-estate-visual::admin.components.selector.data.form.header')),
            Input::make('data.form.text')
                ->title('kelnik-estate-visual::admin.components.selector.data.form.text')
                ->max(DataProvider::FORM_TEXT_MAX_LENGTH),
            Select::make('data.form.id')
                ->title('kelnik-estate-visual::admin.components.selector.data.form.element')
                ->options($formPlatformService->getList())
                ->empty(
                    trans('kelnik-estate-visual::admin.components.selector.data.form.noValue'),
                    DataProvider::NO_VALUE
                )
                ->help('kelnik-estate-visual::admin.components.selector.data.form.help'),
            $formPlatformService->getContentLink()
        ];
    }
}
