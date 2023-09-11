<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\Section;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Estate\Models\Section;
use Kelnik\Estate\Platform\Layouts\Section\EditBaseLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Platform\Services\Contracts\SectionPlatformService;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;

final class EditScreen extends BaseScreen
{
    private bool $exists = false;
    private ?string $title = null;
    protected string $routeToList = 'estate.section.list';
    private SectionPlatformService $sectionPlatformService;

    public ?Section $section = null;

    public function __construct()
    {
        parent::__construct();
        $this->sectionPlatformService = resolve(SectionPlatformService::class);
    }

    public function query(Section $section): array
    {
        $this->name = trans('kelnik-estate::admin.menu.sections');
        $this->exists = $section->exists;

        if ($this->exists) {
            $this->name = $section->title;
        }

        return [
            'section' => $section
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
            \Orchid\Support\Facades\Layout::tabs([
                trans('kelnik-estate::admin.tab.base') => EditBaseLayout::class
            ])
        ];
    }

    public function saveRow(Request $request): RedirectResponse
    {
        return $this->sectionPlatformService->save($this->section, $request);
    }

    public function removeRow(Section $section): RedirectResponse
    {
        return $this->sectionPlatformService->remove($section, $this->routeToList);
    }
}
