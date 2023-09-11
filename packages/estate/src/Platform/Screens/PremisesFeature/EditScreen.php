<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\PremisesFeature;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Estate\Models\PremisesFeatureGroup;
use Kelnik\Estate\Platform\Layouts\PremisesFeature\EditLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Platform\Services\Contracts\PremisesFeatureGroupPlatformService;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;

final class EditScreen extends BaseScreen
{
    private bool $exists = false;
    private ?string $title = null;
    protected string $routeToList = 'estate.pfeature.list';
    private PremisesFeatureGroupPlatformService $premisesFeatureGroupPlatformService;

    public ?PremisesFeatureGroup $feature = null;

    public function __construct()
    {
        parent::__construct();
        $this->premisesFeatureGroupPlatformService = resolve(PremisesFeatureGroupPlatformService::class);
    }

    public function query(PremisesFeatureGroup $feature): array
    {
        $this->name = trans('kelnik-estate::admin.menu.premisesFeatures');
        $this->exists = $feature->exists;
        $feature->load('features');

        if ($this->exists) {
            $this->name = $feature->title;
        }

        return [
            'feature' => $feature
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

    /** @return Layout[]|string[] */
    public function layout(): array
    {
        return [
             EditLayout::class
        ];
    }

    public function saveRow(Request $request): RedirectResponse
    {
        return $this->premisesFeatureGroupPlatformService->save($this->feature, $request);
    }

    public function removeRow(PremisesFeatureGroup $feature): RedirectResponse
    {
        return $this->premisesFeatureGroupPlatformService->remove($feature, $this->routeToList);
    }
}
