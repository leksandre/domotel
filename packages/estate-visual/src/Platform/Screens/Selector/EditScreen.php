<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Screens\Selector;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Platform\Layouts\Selector\EditLayout;
use Kelnik\EstateVisual\Platform\Layouts\Selector\SettingsLayout;
use Kelnik\EstateVisual\Platform\Screens\BaseScreen;
use Kelnik\EstateVisual\Platform\Services\Contracts\SelectorPlatformService;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\PremisesStatusRepository;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\PremisesTypeGroupRepository;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;

final class EditScreen extends BaseScreen
{
    private bool $exists = false;
    private SelectorPlatformService $selectorPlatformService;
    private SettingsService $settingsService;

    public ?Selector $selector = null;

    public function __construct()
    {
        parent::__construct();
        $this->selectorPlatformService = resolve(SelectorPlatformService::class);
        $this->settingsService = resolve(SettingsService::class);
    }

    public function query(Selector $selector): array
    {
        $this->name = trans('kelnik-estate-visual::admin.menu.selector');
        $this->exists = $selector->exists;

        if ($this->exists) {
            $this->name = $selector->title;
        }

        $defColors = $this->settingsService->getDefaultColors();

        return [
            'selector' => $selector,
            'coreService' => $this->coreService,
            'statuses' => resolve(PremisesStatusRepository::class)->getList(),
            'typeGroups' => resolve(PremisesTypeGroupRepository::class )->getListWithTypes(),
            'defColor' => $defColors['brand-base'] ?? '#000000',
            'grayColor' => $defColors['brand-gray'] ?? '#e2e2e2',
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-estate-visual::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('estateVisual.selector.list')),

            Button::make(trans('kelnik-estate-visual::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeRow')
                ->confirm(trans('kelnik-estate-visual::admin.deleteConfirm', ['title' => $this->name]))
                ->canSee($this->exists),
        ];
    }

    /** @return Layout[] */
    public function layout(): array
    {
        return [
            EditLayout::class,
            SettingsLayout::class,
            \Orchid\Support\Facades\Layout::rows([
                Button::make(trans('kelnik-estate-visual::admin.save'))
                    ->icon('bs.save')
                    ->class('btn btn-secondary')
                    ->method('saveRow')
            ])
        ];
    }

    public function saveRow(Request $request): RedirectResponse
    {
        return $this->selectorPlatformService->save($this->selector, $request);
    }

    public function removeRow(Selector $selector): RedirectResponse
    {
        return $this->selectorPlatformService->remove($selector);
    }
}
