<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\View\Components\SelectorFrame\Layouts;

use Kelnik\Core\View\Components\Contracts\HasMargin;
use Kelnik\Estate\Repositories\Contracts\PremisesStatusRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeGroupRepository;
use Kelnik\EstateVisual\Platform\Services\Contracts\SelectorPlatformService;
use Kelnik\EstateVisual\Repositories\Contracts\SelectorRepository;
use Kelnik\EstateVisual\View\Components\SelectorFrame\DataProvider;
use Kelnik\EstateVisual\View\Components\SelectorFrame\SelectorFrame;
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
                ->title('kelnik-estate-visual::admin.components.selectorFrame.data.title'),
            Input::make('data.alias')
                ->title('kelnik-estate-visual::admin.components.selectorFrame.data.alias')
                ->mask(['regex' => '[a-z0-9\-_]+'])
                ->maxlength(DataProvider::ALIAS_MAX_LENGTH),
            Switcher::make('active')
                ->title('kelnik-estate-visual::admin.active')
                ->sendTrueOrFalse(),
            Select::make('data.selector_id')
                ->title('kelnik-estate-visual::admin.components.selectorFrame.data.selector')
                ->options($selectorRepo->getAll()->pluck('title', 'id')->toArray())
                ->empty(trans('kelnik-estate-visual::admin.noValue'), DataProvider::NO_VALUE)
                ->required(),
            Select::make('data.types')
                ->title('kelnik-estate-visual::admin.components.selectorFrame.data.types')
                ->options($typeGroupRepo->getAll()->pluck('title', 'id')->toArray())
                ->multiple(),
            Select::make('data.statuses')
                ->title('kelnik-estate-visual::admin.components.selectorFrame.data.statuses')
                ->options($statusRepo->getAll()->pluck('title', 'id')->toArray())
                ->multiple(),
            Select::make('data.template')
                ->title('kelnik-estate-visual::admin.components.selectorFrame.template')
                ->options(SelectorFrame::getTemplates()->pluck('title', 'name')->toArray())
                ->required(),
            Select::make('data.frameTemplate')
                ->title('kelnik-estate-visual::admin.components.selectorFrame.frameTemplate')
                ->options(SelectorFrame::getFrameTemplates()->pluck('title', 'name')->toArray())
                ->required(),
            Select::make('data.margin.top')
                ->title('kelnik-core::admin.margin.top')
                ->options(SelectorFrame::getMarginVariants())
                ->value(HasMargin::MARGIN_DEFAULT)
                ->addBeforeRender(fn() => $this->set('isOptionList', false)),
            Select::make('data.margin.bottom')
                ->title('kelnik-core::admin.margin.bottom')
                ->options(SelectorFrame::getMarginVariants())
                ->value(HasMargin::MARGIN_DEFAULT)
                ->addBeforeRender(fn() => $this->set('isOptionList', false)),

            $service->getContentLink($this->query->get('data')['selector_id'] ?? 0),
        ];
    }
}
