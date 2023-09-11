<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\View\Components\HowToBuy\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Form\Platform\Services\Contracts\FormPlatformService;
use Kelnik\Mortgage\Platform\Fields\Variants;
use Kelnik\Mortgage\View\Components\HowToBuy\DataProvider;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class ContentLayout extends Rows
{
    public function __construct(private Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        $coreService = $this->query->get('coreService');

        $res = [
            Input::make('data.content.title')
                ->title('kelnik-mortgage::admin.components.howToBuy.titleField')
                ->placeholder('kelnik-mortgage::admin.components.howToBuy.titlePlaceholder')
                ->maxlength(255),

            Quill::make('data.content.text')
                ->title('kelnik-mortgage::admin.components.howToBuy.text')
                ->help(trans(
                    'kelnik-mortgage::admin.components.howToBuy.textHelp',
                    ['limit' => DataProvider::TEXT_LIMIT]
                )),

            Quill::make('data.content.factoidText')
                ->title('kelnik-mortgage::admin.components.howToBuy.factoidText')
                ->help(trans(
                    'kelnik-mortgage::admin.components.howToBuy.factoidTextHelp',
                    ['limit' => DataProvider::FACTOID_TEXT_LIMIT]
                ))
                ->hr(),

            Title::make('')->value(trans('kelnik-mortgage::admin.components.howToBuy.variants.title')),
            Variants::make('data.content.variants')
                ->columns([
                    trans('kelnik-mortgage::admin.active') => 'active_',
                    trans('kelnik-mortgage::admin.title') => 'title_'
                ])
                ->fields([
                    'active_' => Switcher::make()
                        ->set('data-action', 'input->kelnik-mortgage-variants#updateActive')
                        ->set('data-field', 'active_')
                        ->set('data-origin', 'active'),
                    'title_' => Input::make()
                        ->maxlength(255)
                        ->set('data-action', 'input->kelnik-mortgage-variants#updateTitle')
                        ->set('data-field', 'title_')
                        ->set('data-origin', 'title')
                ])
                ->sortable(true)
                ->set('data-kelnik-mortgage-variants-modal', 'variant')
                ->maxRows(10)
                ->help('kelnik-mortgage::admin.components.howToBuy.variants.maxCount'),

            Switcher::make('data.content.openFirstVariant')
                ->title('kelnik-mortgage::admin.components.howToBuy.openFirstVariant')
                ->sendTrueOrFalse()
                ->hr()
        ];

        if ($coreService->hasModule('form')) {
            /** @var FormPlatformService $formService */
            $formService = resolve(FormPlatformService::class);
            $res[] = Title::make('')->value(trans('kelnik-mortgage::admin.components.howToBuy.button.header'));
            $res[] = Input::make('data.content.button.text')
                ->title('kelnik-mortgage::admin.components.howToBuy.button.text')
                ->help(trans(
                    'kelnik-mortgage::admin.components.howToBuy.button.textHelp',
                    ['limit' => DataProvider::BUTTON_TEXT_LIMIT]
                ))
                ->maxlength(DataProvider::BUTTON_TEXT_LIMIT);

            $res[] = Select::make('data.content.button.form_id')
                ->title('kelnik-mortgage::admin.components.howToBuy.button.form')
                ->options($formService->getList())
                ->empty(trans('kelnik-mortgage::admin.components.howToBuy.noValue'), DataProvider::NO_VALUE);

            $res[] = $formService->getContentLink();
        }

        $res[] = is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter;

        return $res;
    }
}
