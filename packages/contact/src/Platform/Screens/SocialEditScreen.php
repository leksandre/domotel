<?php

declare(strict_types=1);

namespace Kelnik\Contact\Platform\Screens;

use Illuminate\Http\RedirectResponse;
use Kelnik\Contact\Http\Requests\SocialSaveRequest;
use Kelnik\Contact\Models\SocialLink;
use Kelnik\Contact\Platform\Layouts\Social\EditLayout;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Support\Facades\Toast;

final class SocialEditScreen extends Screen
{
    private bool $exists = false;
    private ?string $title = null;

    public ?SocialLink $social = null;

    public function query(SocialLink $social): array
    {
        $this->name = trans('kelnik-contact::admin.menu.title');
        $this->exists = $social->exists;

        if ($this->exists) {
            $this->name = $social->title;
        }

        return [
            'social' => $social
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-contact::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('contact.social.list')),

            Button::make(trans('kelnik-contact::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeSocial')
                ->confirm(trans('kelnik-contact::admin.deleteConfirm', ['title' => $this->title]))
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

    public function saveSocial(SocialSaveRequest $request): RedirectResponse
    {
        $social = $this->social;
        $social->fill($request->getDto()->toArray());

        if ($this->socialLinkRepository->save($social)) {
            Toast::info(trans('kelnik-contact::admin.saved'));
        }

        return redirect()->route(
            $this->coreService->getFullRouteName('contact.social.list')
        );
    }

    public function removeSocial(SocialLink $social): RedirectResponse
    {
        $this->socialLinkRepository->delete($social)
            ? Toast::info(trans('kelnik-contact::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('contact.social.list'));
    }
}
