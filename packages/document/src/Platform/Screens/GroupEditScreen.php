<?php

declare(strict_types=1);

namespace Kelnik\Document\Platform\Screens;

use Illuminate\Http\RedirectResponse;
use Kelnik\Document\Http\Requests\GroupSaveRequest;
use Kelnik\Document\Models\Group;
use Kelnik\Document\Platform\Layouts\Group\GroupLayout;
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
        $this->name = trans('kelnik-document::admin.menu.groups');
        $this->exists = $group->exists;

        if ($this->exists) {
            $this->name = $group->title;
        }

        return [
            'group' => $group
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-document::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('document.groups')),

            Button::make(trans('kelnik-document::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeCamera')
                ->confirm(trans('kelnik-document::admin.deleteConfirm', ['title' => $this->title]))
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
        Toast::info(trans('kelnik-document::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('document.groups'));
    }

    public function removeGroup(Group $group): RedirectResponse
    {
        $this->groupRepository->delete($group)
            ? Toast::info(trans('kelnik-document::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('document.groups'));
    }
}
