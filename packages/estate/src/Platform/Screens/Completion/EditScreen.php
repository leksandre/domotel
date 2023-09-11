<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\Completion;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Estate\Models\Completion;
use Kelnik\Estate\Platform\Layouts\Completion\EditLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Platform\Services\Contracts\CompletionPlatformService;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;

final class EditScreen extends BaseScreen
{
    private bool $exists = false;
    private ?string $title = null;
    protected string $routeToList = 'estate.completion.list';
    private CompletionPlatformService $completionPlatformService;

    public ?Completion $completion = null;

    public function __construct()
    {
        parent::__construct();
        $this->completionPlatformService = resolve(CompletionPlatformService::class);
    }

    public function query(Completion $completion): array
    {
        $this->name = trans('kelnik-estate::admin.menu.completions');
        $this->exists = $completion->exists;

        if ($this->exists) {
            $this->name = $completion->title;
        }

        return [
            'completion' => $completion
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
        return $this->completionPlatformService->save($this->completion, $request);
    }

    public function removeRow(Completion $completion): RedirectResponse
    {
        return $this->completionPlatformService->remove($completion, $this->routeToList);
    }
}
