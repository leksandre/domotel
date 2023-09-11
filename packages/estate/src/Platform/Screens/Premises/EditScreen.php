<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\Premises;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Platform\Layouts\Premises\EditAreaLayout;
use Kelnik\Estate\Platform\Layouts\Premises\EditBaseLayout;
use Kelnik\Estate\Platform\Layouts\Premises\EditFeatureLayout;
use Kelnik\Estate\Platform\Layouts\Premises\EditImageLayout;
use Kelnik\Estate\Platform\Layouts\Premises\EditPlanoplanLayout;
use Kelnik\Estate\Platform\Layouts\Premises\EditPriceLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Platform\Services\Contracts\PremisesPlatformService;
use Kelnik\Estate\Platform\Services\Contracts\PremisesStatusPlatformService;
use Kelnik\Estate\Platform\Services\Contracts\PremisesTypeGroupPlatformService;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;

final class EditScreen extends BaseScreen
{
    private bool $exists = false;
    private ?string $title = null;
    protected string $routeToList = 'estate.premises.list';
    private PremisesStatusPlatformService $premisesStatusPlatformService;
    private PremisesTypeGroupPlatformService $premisesTypeGroupPlatformService;
    private PremisesPlatformService $premisesPlatformService;

    protected ?string $name = null;

    public ?Premises $premises = null;

    public function __construct()
    {
        parent::__construct();
        $this->premisesStatusPlatformService = resolve(PremisesStatusPlatformService::class);
        $this->premisesTypeGroupPlatformService = resolve(PremisesTypeGroupPlatformService::class);
        $this->premisesPlatformService = resolve(PremisesPlatformService::class);
    }

    public function query(Premises $premises): array
    {
        $this->name = trans('kelnik-estate::admin.menu.premises');
        $this->exists = $premises->exists;

        if ($this->exists) {
            $this->name = trans('kelnik-estate::admin.premises.title') . ': ' . $premises->title;
        }

        return [
            'premises' => $premises,
            'types' => $this->premisesTypeGroupPlatformService->getList(),
            'statuses' => $this->premisesStatusPlatformService->getList()
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-estate::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName($this->routeToList)),

            Button::make(trans('kelnik-estate::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeRow')
                ->confirm(trans('kelnik-estate::admin.deleteConfirm', ['title' => $this->title]))
                ->canSee($this->exists),
        ];
    }

    /** @return Layout[] */
    public function layout(): array
    {
        return [
            \Orchid\Support\Facades\Layout::tabs([
                trans('kelnik-estate::admin.tab.base') => EditBaseLayout::class,
                trans('kelnik-estate::admin.tab.prices') => EditPriceLayout::class,
                trans('kelnik-estate::admin.tab.areas') => EditAreaLayout::class,
                trans('kelnik-estate::admin.tab.features') => EditFeatureLayout::class,
                trans('kelnik-estate::admin.tab.images') => EditImageLayout::class,
                trans('kelnik-estate::admin.tab.planoplan') => EditPlanoplanLayout::class,
            ])
        ];
    }

    public function saveRow(Request $request): RedirectResponse
    {
        return $this->premisesPlatformService->save($this->premises, $request);
    }

    public function removeRow(Premises $premises): RedirectResponse
    {
        return $this->premisesPlatformService->remove($premises, $this->routeToList);
    }
}
