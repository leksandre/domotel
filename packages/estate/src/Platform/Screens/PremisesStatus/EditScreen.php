<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\PremisesStatus;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\Estate\Platform\Layouts\PremisesStatus\EditLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Platform\Services\Contracts\PremisesStatusPlatformService;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;

final class EditScreen extends BaseScreen
{
    private bool $exists = false;
    private ?string $title = null;
    protected string $routeToList = 'estate.pstatus.list';
    private PremisesStatusPlatformService $premisesStatusPlatformService;

    public ?PremisesStatus $status = null;

    public function __construct()
    {
        parent::__construct();
        $this->premisesStatusPlatformService = resolve(PremisesStatusPlatformService::class);
    }

    public function query(PremisesStatus $status): array
    {
        $this->name = trans('kelnik-estate::admin.menu.premisesStatuses');
        $this->exists = $status->exists;

        if ($this->exists) {
            $this->name = $status->title;
        }

        return [
            'status' => $status
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
             EditLayout::class
        ];
    }

    public function saveRow(Request $request): RedirectResponse
    {
        return $this->premisesStatusPlatformService->save($this->status, $request);
    }

    public function removeRow(PremisesStatus $status): RedirectResponse
    {
        return $this->premisesStatusPlatformService->remove($status, $this->routeToList);
    }
}
