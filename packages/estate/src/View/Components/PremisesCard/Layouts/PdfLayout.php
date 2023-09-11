<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\PremisesCard\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Core\Platform\Fields\Upload;
use Kelnik\Core\Services\Contracts\UploadService;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Kelnik\Estate\View\Components\PremisesCard\DataProvider;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class PdfLayout extends Rows
{
    public function __construct(private readonly Fieldable|Groupable|Closure $tabFooter)
    {
//        $this->title(trans('kelnik-estate::admin.components.premisesCard.pdf.title'));
    }

    protected function fields(): array
    {
        /** @var UploadService $uploadService */
        $uploadService = resolve(UploadService::class);

        return [
            Matrix::make('data.pdf.phone')
                ->title('kelnik-estate::admin.components.premisesCard.pdf.phone')
                ->sortable(true)
                ->maxRows(DataProvider::PHONE_MAX_CNT)
                ->columns(['' => 'value'])
                ->fields([
                    'value' => Input::make()
                        ->mask(['regex' => DataProvider::PHONE_REGEXP])
                        ->maxlength(DataProvider::PHONE_LIMIT)
                ])
                ->help('kelnik-estate::admin.components.premisesCard.pdf.phoneHelp'),

            Matrix::make('data.pdf.schedule')
                ->title('kelnik-estate::admin.components.premisesCard.pdf.schedule')
                ->sortable(true)
                ->maxRows(DataProvider::SCHEDULE_MAX_CNT)
                ->columns(['' => 'value'])
                ->fields([
                    'value' => Input::make()->maxlength(DataProvider::SCHEDULE_LIMIT)
                ]),

            Title::make('')->value(trans('kelnik-estate::admin.components.premisesCard.pdf.about.header')),
            Input::make('data.pdf.about.title')
                ->title('kelnik-estate::admin.components.premisesCard.pdf.about.title')
                ->maxlength(DataProvider::ABOUT_TITLE_LIMIT),
            Input::make('data.pdf.about.address')
                ->title('kelnik-estate::admin.components.premisesCard.pdf.about.address')
                ->maxlength(DataProvider::ABOUT_ADDRESS_LIMIT),
            Upload::make('data.pdf.about.images')
                ->title('kelnik-estate::admin.components.premisesCard.pdf.about.images')
                ->maxFiles(DataProvider::ABOUT_IMAGES_LIMIT)
                ->groups(EstateServiceProvider::MODULE_NAME)
                ->acceptedFiles('image/*')
                ->resizeWidth(1920)
                ->resizeHeight(1080)
                ->chunking(true)
                ->chunkSize($uploadService->getMaxUploadSize()),
            Quill::make('data.pdf.about.text')
                ->title('kelnik-estate::admin.components.premisesCard.pdf.about.text'),

            Matrix::make('data.pdf.about.utp')
                ->title('kelnik-estate::admin.components.premisesCard.pdf.about.utp')
                ->sortable(true)
                ->maxRows(DataProvider::UTP_CNT_MAX)
                ->columns([
                    trans('kelnik-estate::admin.components.premisesCard.pdf.about.utpTitle') => 'title',
                    trans('kelnik-estate::admin.components.premisesCard.pdf.about.utpText') => 'text'
                ])
                ->fields([
                    'title' => Input::make()->maxlength(DataProvider::UTP_TITLE_LIMIT),
                    'text' => Input::make()->maxlength(DataProvider::UTP_TEXT_LIMIT)
                ]),

            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
