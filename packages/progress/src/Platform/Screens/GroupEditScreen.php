<?php

declare(strict_types=1);

namespace Kelnik\Progress\Platform\Screens;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Progress\Http\Requests\GroupSaveRequest;
use Kelnik\Progress\Models\Group;
use Kelnik\Progress\Platform\Layouts\Group\GroupLayout;
use Kelnik\Progress\Repositories\Contracts\GroupRepository;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Support\Facades\Toast;

final class GroupEditScreen extends Screen
{
    private bool $exists = false;
    private ?string $title = null;

    public ?Group $group = null;

    public function query(Group $group): array
    {
        $this->name = trans('kelnik-progress::admin.menu.groups');
        $this->exists = $group->exists;

        if ($this->exists) {
            $this->name = $this->title = $group->title;
        }

        return [
            'group' => $group
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-progress::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('progress.groups')),

            Button::make(trans('kelnik-progress::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeCamera')
                ->confirm(trans('kelnik-progress::admin.deleteConfirm', ['title' => $this->title]))
                ->canSee($this->exists),
        ];
    }

    /** @return Layout[]|class-string[] */
    public function layout(): array
    {
        return [
            GroupLayout::class,
        ];
    }

    public function saveGroup(GroupSaveRequest $request): RedirectResponse
    {
        $group = $this->group;
        $dto = $request->getDto();

        $group->title = $dto->title;
        $group->active = $dto->active;
        $group->user()->associate($dto->user);

        $this->groupRepository->save($group);
        Toast::info(trans('kelnik-progress::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('progress.groups'));
    }

    public function removeGroup(Group $group): RedirectResponse
    {
        $this->groupRepository->delete($group)
            ? Toast::info(trans('kelnik-progress::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('progress.groups'));
    }
}
