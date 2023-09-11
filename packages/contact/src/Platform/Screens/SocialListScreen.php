<?php

declare(strict_types=1);

namespace Kelnik\Contact\Platform\Screens;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Kelnik\Contact\Http\Requests\ElementSortRequest;
use Kelnik\Contact\Platform\Layouts\Social\ListLayout;
use Kelnik\Contact\Repositories\Contracts\SocialLinkRepository;
use Orchid\Screen\Actions\Link;

final class SocialListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-contact::admin.menu.social');

        return [
            'list' => resolve(SocialLinkRepository::class)->getAll(),
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('contact.social.sort'),
                [],
                false
            ),
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-contact::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName('contact.social.edit'))
        ];
    }

    public function layout(): array
    {
        return [
            ListLayout::class
        ];
    }

    public function sortable(ElementSortRequest $request): JsonResponse
    {
        $this->contactService->sortSocial($request->getDto());

        return Response::json([
            'success' => true,
            'messages' => [trans('kelnik-contact::admin.success')]
        ]);
    }
}
