<?php

declare(strict_types=1);

namespace Kelnik\Progress\Platform\Screens;

use Illuminate\Http\RedirectResponse;
use Kelnik\Progress\Http\Requests\AlbumSaveRequest;
use Kelnik\Progress\Models\Album;
use Kelnik\Progress\Platform\Layouts\Album\AlbumLayout;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Support\Facades\Toast;

final class AlbumEditScreen extends Screen
{
    private bool $exists = false;
    private ?string $title = null;

    public ?Album $album = null;

    public function query(Album $album): array
    {
        $this->name = trans('kelnik-progress::admin.menu.title');
        $this->exists = $album->exists;

        if ($this->exists) {
            $this->name = $this->title = $album->title;
        }

        return [
            'album' => $album,
            'videos' => $album->videos
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-progress::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('progress.albums')),

            Button::make(trans('kelnik-progress::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeAlbum')
                ->confirm(trans('kelnik-progress::admin.deleteConfirm', ['title' => $this->title]))
                ->canSee($this->exists),
        ];
    }

    /** @return Layout[]|class-string[] */
    public function layout(): array
    {
        return [
            AlbumLayout::class,
        ];
    }

    public function saveAlbum(AlbumSaveRequest $request): RedirectResponse
    {
        $album = $this->album;
        $dto = $request->getDto();
        $album->title = $dto->title;
        $album->active = $dto->active;
        $album->publish_date = $dto->publish_date;
        $album->description = $dto->description;
        $album->comment = $dto->comment;
        $album->user()->associate($dto->user);
        $album->group()->disassociate();
        $album->touch();

        if ($dto->group) {
            $album->group()->associate($dto->group);
        }

        $this->albumRepository->save($album, $dto->images, $dto->videos);
        Toast::info(trans('kelnik-progress::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('progress.albums'));
    }

    public function removeAlbum(Album $album): RedirectResponse
    {
        $this->albumRepository->delete($album)
            ? Toast::info(trans('kelnik-progress::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('progress.albums'));
    }
}
