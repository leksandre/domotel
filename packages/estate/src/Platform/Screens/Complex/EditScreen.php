<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\Complex;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Platform\Layouts\Complex\EditBaseLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Platform\Services\Contracts\ComplexPlatformService;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;

final class EditScreen extends BaseScreen
{
    private bool $exists = false;
    private ?string $title = null;
    protected string $routeToList = 'estate.complex.list';
    private ComplexPlatformService $complexPlatformService;

    public ?Complex $complex = null;

    public function __construct()
    {
        parent::__construct();
        $this->complexPlatformService = resolve(ComplexPlatformService::class);
    }

    public function query(Complex $complex): array
    {
        $this->name = trans('kelnik-estate::admin.menu.complexes');
        $this->exists = $complex->exists;

        if ($this->exists) {
            $this->name = $complex->title;
        }

        return [
            'complex' => $complex
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
                trans('kelnik-estate::admin.tab.base') => EditBaseLayout::class
            ])
        ];
    }

    public function saveRow(Request $request): RedirectResponse
    {
        return $this->complexPlatformService->save($this->complex, $request);
    }

    public function removeRow(Complex $complex): RedirectResponse
    {
        return $this->complexPlatformService->remove($complex, $this->routeToList);
    }
}
