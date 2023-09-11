<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Layouts\Form;

use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Core\Platform\Fields\Slug;
use Kelnik\Page\Services\Contracts\PageService;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class EditLayout extends Rows
{
    protected function fields(): array
    {
        $coreService = $this->query->get('coreService');

        $res = [
            Input::make('form.title')
                ->title('kelnik-form::admin.title')
                ->id('field-form-title')
                ->maxlength(255)
                ->required(),
            Slug::make('form.slug')
                ->title('kelnik-form::admin.slug')
                ->maxlength(255)
                ->required()
                ->source('field-form-title')
                ->method('transliterate'),
            Switcher::make('form.active')->title('kelnik-form::admin.active')->sendTrueOrFalse(),
            Input::make('form.button_text')
                ->title('kelnik-form::admin.buttonText')
                ->maxlength(255)
                ->value(trans('kelnik-form::admin.buttonTextValue'))
        ];

        if ($coreService->hasModule('page')) {
            $res[] = Select::make('form.policy_page_id')
                ->title('kelnik-form::admin.policyPage')
                ->options(
                    resolve(PageService::class)
                        ->getPagesWithoutDynamicComponents()
                        ->pluck('title', 'id')
                );
        }

        return array_merge(
            $res,
            [
                Quill::make('form.description')->title('kelnik-form::admin.description'),
                Input::make('form.success_title')
                    ->title('kelnik-form::admin.successTitle')
                    ->maxlength(255),
                Quill::make('form.success_text')->title('kelnik-form::admin.successText'),
                Input::make('form.error_title')
                    ->title('kelnik-form::admin.errorTitle')
                    ->maxlength(255),
                Quill::make('form.error_text')->title('kelnik-form::admin.errorText'),
                Button::make(trans('kelnik-form::admin.save'))
                    ->icon('bs.save')
                    ->class('btn btn-secondary')
                    ->method('saveForm')
            ]
        );
    }
}
