<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Platform\Layouts\Block;

use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Core\Platform\Fields\Upload;
use Kelnik\FBlock\Platform\Services\BlockPlatformService;
use Kelnik\FBlock\Providers\FBlockServiceProvider;
use Kelnik\Form\Platform\Services\Contracts\FormPlatformService;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class BaseLayout extends Rows
{
    protected function fields(): array
    {
        $button = $this->query['element']->button;
        $coreService = $this->query->get('coreService');

        $fields = [
            Switcher::make('element.active')
                ->title('kelnik-fblock::admin.active')
                ->sendTrueOrFalse(),
            Input::make('element.title')
                ->title('kelnik-fblock::admin.title')
                ->maxlength(255)
                ->required()
                ->hr(),

            Input::make('element.area')
                ->title('kelnik-fblock::admin.area')
                ->maxlength(255),
            Input::make('element.floor')
                ->title('kelnik-fblock::admin.floor')
                ->maxlength(255),
            Input::make('element.price')
                ->title('kelnik-fblock::admin.price')
                ->maxlength(255),
            Input::make('element.planoplan_code')
                ->title('kelnik-fblock::admin.planoplan_code')
                ->maxlength(255)
                ->hr(),
            Matrix::make('element.features')
                ->title('kelnik-fblock::admin.features')
                ->sortable(true)
                ->maxRows(BlockPlatformService::FEATURE_MAX_COUNT)
                ->columns([
                    trans('kelnik-fblock::admin.title') => 'title',
                ])
                ->fields([
                    'title' => Input::make()->maxlength(255)->required(),
                ]),
            Upload::make('element.images')
                ->title('kelnik-fblock::admin.images')
                ->acceptedFiles('image/*')
                ->resizeWidth(1920)
                ->resizeHeight(1080)
                ->maxFiles(20)
                ->help('kelnik-fblock::admin.imagesHelp')
                ->groups(FBlockServiceProvider::MODULE_NAME)
                ->hr()
        ];

        if ($coreService->hasModule('form')) {
            /** @var FormPlatformService $formPlatformService */
            $formPlatformService = resolve(FormPlatformService::class);
            $fields[] = Title::make('')->value(trans('kelnik-fblock::admin.button.title'));
            $fields[] = Input::make('element.button.text')
                ->title('kelnik-fblock::admin.button.text')
                ->maxlength(BlockPlatformService::BUTTON_TEXT_LIMIT)
                ->value($button->getText() ?: trans('kelnik-fblock::admin.button.defaultText'));

            $fields[] = Select::make('element.button.formKey')
                ->title('kelnik-fblock::admin.button.form')
                ->options($formPlatformService->getList())
                ->empty(trans('kelnik-fblock::admin.button.noValue'), BlockPlatformService::NO_VALUE)
                ->value($button->getFormKey());

            $fields[] = $formPlatformService->getContentLink()->hr();
        }

        $fields[] = Button::make(trans('kelnik-fblock::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveElement');

        return $fields;
    }
}
