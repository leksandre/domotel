<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\PremisesCard\Layouts;

use Closure;
use Kelnik\Estate\View\Components\PremisesCard\DataProvider;
use Kelnik\Form\Platform\Services\Contracts\FormPlatformService;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

final class FormLayout extends Rows
{
    public function __construct(private readonly Fieldable|Groupable|Closure $tabFooter)
    {
//        $this->title(trans('kelnik-estate::admin.components.premisesCard.callback.title'));
    }

    protected function fields(): array
    {
        /** @var FormPlatformService $formPlatformService */
        $formPlatformService = resolve(FormPlatformService::class);

        return [
            Input::make('data.callbackButton.text')
                ->title('kelnik-page::admin.components.header.callback.text')
                ->help(trans(
                    'kelnik-page::admin.components.header.callback.limit',
                    ['limit' => DataProvider::BUTTON_TEXT_LIMIT]
                ))
                ->maxlength(DataProvider::BUTTON_TEXT_LIMIT),

            Select::make('data.callbackButton.form_id')
                ->title('kelnik-page::admin.components.header.callback.form')
                ->options($formPlatformService->getList())
                ->empty(trans('kelnik-page::admin.components.header.noValue'), DataProvider::NO_VALUE),

            $formPlatformService->getContentLink(),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
