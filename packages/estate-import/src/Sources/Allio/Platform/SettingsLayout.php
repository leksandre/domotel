<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Allio\Platform;

use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Complex;
use Kelnik\EstateImport\Sources\Contracts\SourceType;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
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
                ->title('kelnik-estate-import::admin.source.allio.url')
                ->required(),
            Input::make('settings.' . $sourceName . '.client.login')
                ->title('kelnik-estate-import::admin.source.allio.login')
                ->required(),
            Input::make('settings.' . $sourceName . '.client.password')
                ->title('kelnik-estate-import::admin.source.allio.password')
                ->required(),
            Input::make('settings.' . $sourceName . '.client.developer')
                ->type('number')
                ->min(0)
                ->title('kelnik-estate-import::admin.source.allio.developer')
                ->required(),
            Select::make('settings.' . $sourceName . '.complex')
                ->fromModel(Complex::class, 'title')
                ->title('kelnik-estate-import::admin.source.allio.complex')
                ->required(),
            Link::make(trans('kelnik-estate-import::admin.source.check'))
                ->icon('refresh')
                ->class('btn btn-info')
                ->set('data-controller', 'estate-import_api-checker')
                ->set('turbo', false)
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
