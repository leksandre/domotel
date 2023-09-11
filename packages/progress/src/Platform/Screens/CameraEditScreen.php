<?php

declare(strict_types=1);

namespace Kelnik\Progress\Platform\Screens;

use Illuminate\Http\RedirectResponse;
use Kelnik\Progress\Http\Requests\CameraSaveRequest;
use Kelnik\Progress\Models\Camera;
use Kelnik\Progress\Platform\Layouts\Camera\CameraLayout;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Support\Facades\Toast;

final class CameraEditScreen extends Screen
{
    private bool $exists = false;
    private ?string $title = null;

    public ?Camera $camera = null;

    public function query(Camera $camera): array
    {
        $this->name = trans('kelnik-progress::admin.menu.cameras');
        $this->exists = $camera->exists;

        if ($this->exists) {
            $this->name = $this->title = $camera->title;
        }

        return [
            'camera' => $camera
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-progress::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('progress.cameras')),

            Button::make(trans('kelnik-progress::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeCamera')
                ->confirm(trans('kelnik-progress::admin.deleteConfirm', ['title' => $this->title]))
                ->canSee($this->exists),
        ];
    }

    /** @return Layout[]|string[] */
    public function layout(): array
    {
        return [
            CameraLayout::class,
        ];
    }

    public function saveCamera(CameraSaveRequest $request): RedirectResponse
    {
        $camera = $this->camera;
        $dto = $request->getDto();

        $camera->title = $dto->title;
        $camera->url = $dto->url;
        $camera->active = $dto->active;
        $camera->user()->associate($dto->user);
        $camera->group()->dissociate();

        if ($dto->group) {
            $camera->group()->associate($dto->group);
        }

        $this->cameraRepository->save($camera);
        Toast::info(trans('kelnik-progress::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('progress.cameras'));
    }

    public function removeCamera(Camera $camera): RedirectResponse
    {
        $this->cameraRepository->delete($camera)
            ? Toast::info(trans('kelnik-progress::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('progress.cameras'));
    }
}
