<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\View\Components\Selector\Layouts;

use Kelnik\Estate\Repositories\Contracts\PremisesStatusRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeGroupRepository;
use Kelnik\EstateVisual\Platform\Services\Contracts\SelectorPlatformService;
use Kelnik\EstateVisual\Repositories\Contracts\SelectorRepository;
use Kelnik\EstateVisual\View\Components\Selector\Selector;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class SettingsLayout extends Rows
{
    protected function fields(): array
    {
        $typeGroupRepo = resolve(PremisesTypeGroupRepository::class);
        $statusRepo = resolve(PremisesStatusRepository::class);
        $selectorRepo = resolve(SelectorRepository::class);
        /** @var SelectorPlatformService $service */
        $service = resolve(SelectorPlatformService::class);

        return [
            Input::make('data.title')
                ->title('kelnik-estate-visual::admin.components.selector.data.title'),
            Switcher::make('active')
                ->title('kelnik-estate-visual::admin.active')
                ->sendTrueOrFalse(),
            Select::make('data.selector_id')
                ->title('kelnik-estate-visual::admin.components.selector.data.selector')
                ->options($selectorRepo->getAll()->pluck('title', 'id')->toArray())
                ->required(),
            Select::make('data.types')
                ->title('kelnik-estate-visual::admin.components.selector.data.types')
                ->options($typeGroupRepo->getAll()->pluck('title', 'id')->toArray())
                ->multiple(),
            Select::make('data.statuses')
                ->title('kelnik-estate-visual::admin.components.selector.data.statuses')
                ->options($statusRepo->getAll()->pluck('title', 'id')->toArray())
                ->multiple(),
            Select::make('data.template')
                ->title('kelnik-estate-visual::admin.components.selector.template')
                ->options(Selector::getTemplates()->pluck('title', 'name')->toArray())
                ->required(),

            $service->getContentLink($this->query->get('data')['selector_id'] ?? 0),
        ];
    }
}
