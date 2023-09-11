<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Csv\Platform;

use Kelnik\Core\Platform\Fields\Upload;
use Kelnik\Core\Services\Contracts\UploadService;
use Kelnik\EstateImport\Providers\EstateImportServiceProvider;
use Kelnik\EstateImport\Sources\Contracts\SourceType;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Layouts\Rows;

final class UploadLayout extends Rows
{
    protected function fields(): array
    {
        /** @var UploadService $uploadService */
        $uploadService = resolve(UploadService::class);

        /** @var SourceType $source */
        $source = $this->query->get('source');

        $this->title = trans('kelnik-estate-import::admin.header.sourceTypes.' . $source->getName());

        return [
            Upload::make('file')
                ->help('kelnik-estate-import::admin.history.importFileHelp')
                ->title('kelnik-estate-import::admin.history.importFile')
                ->maxFiles(1)
                ->chunking(true)
                ->chunkSize($uploadService->getMaxUploadSize())
                ->groups(EstateImportServiceProvider::MODULE_NAME)
                ->acceptedFiles('text/csv')
                ->required(),

            Upload::make('images')
                ->title('kelnik-estate-import::admin.history.importImagesArch')
                ->help('kelnik-estate-import::admin.history.importImagesArchHelp')
                ->maxFiles(1)
                ->chunking(true)
                ->chunkSize($uploadService->getMaxUploadSize())
                ->groups(EstateImportServiceProvider::MODULE_NAME)
                ->acceptedFiles('application/zip'),

            Button::make(trans('kelnik-estate-import::admin.history.uploadFile'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('addImportFile')
        ];
    }
}
