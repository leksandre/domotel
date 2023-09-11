<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\ProfitBase\Platform;

use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateImport\Sources\Contracts\SourceType;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class SettingsLayout extends Rows
{
    protected function fields(): array
    {
        /** @var SourceType $source */
        $source = $this->query->get('source');
        $this->title = trans('kelnik-estate-import::admin.header.sourceTypes.' . $source->getName());
        $sourceName = $source->getName();

        /** @var CoreService $coreService */
        $coreService = $this->query->get('coreService');

        return [
            Input::make('settings.' . $sourceName . '.client.url')
                ->type('url')
                ->title('kelnik-estate-import::admin.source.profitBase.url')
                ->required(),
            Input::make('settings.' . $sourceName . '.client.key')
                ->title('kelnik-estate-import::admin.source.profitBase.key')
                ->required(),
            Link::make(trans('kelnik-estate-import::admin.source.check'))
                ->icon('refresh')
                ->class('btn btn-info')
                ->set('data-controller', 'estate-import_api-checker')
                ->set('turbo', false)
//                ->method('checkApiConnection')
                ->set(
                    'data-route',
                    route(
                        $coreService->getFullRouteName('estateImport.settings'),
                        ['method' => 'checkApiConnection']
                    )
                )
                ->set('data-action', 'click->estate-import_api-checker#submit')
                ->set('data-estate-import_api-checker-target', 'button'),

            Title::make('')
                ->value(
                    trans('kelnik-estate-import::admin.scheduleNextDueDate') .
                    $this->query->get('importPlatformService')->getScheduleNextDueDate()
                )
        ];
    }
}
