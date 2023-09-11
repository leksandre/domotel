<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Xml\Platform;

use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\EstateImport\Sources\Contracts\SourceType;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class SourceListLayout extends Rows
{
    protected function fields(): array
    {
        /** @var SourceType $source */
        $source = $this->query->get('source');
        $this->title = trans('kelnik-estate-import::admin.header.sourceTypes.' . $source->getName());

        return [
            Matrix::make('settings.' . $source->getName() . '.list')
                ->maxRows(10)
                ->columns([trans('kelnik-estate-import::admin.history.source.url') => 'url'])
                ->fields(['url' => Input::make()->type('url')]),

            Title::make('')
                ->value(
                    trans('kelnik-estate-import::admin.scheduleNextDueDate') .
                    $this->query->get('importPlatformService')->getScheduleNextDueDate()
                )
        ];
    }
}
