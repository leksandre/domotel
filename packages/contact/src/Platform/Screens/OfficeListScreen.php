<?php

declare(strict_types=1);

namespace Kelnik\Contact\Platform\Screens;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Kelnik\Contact\Http\Requests\ElementSortRequest;
use Kelnik\Contact\Platform\Layouts\Office\ListLayout;
use Kelnik\Contact\Repositories\Contracts\OfficeRepository;
use Orchid\Screen\Actions\Link;

final class OfficeListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-contact::admin.menu.offices');

        return [
            'list' => resolve(OfficeRepository::class)->getAll(),
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('contact.office.sort'),
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
                ->route($this->coreService->getFullRouteName('contact.office.edit'))
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
        $this->contactService->sortOffices($request->getDto());

        return Response::json([
            'success' => true,
            'messages' => [trans('kelnik-contact::admin.success')]
        ]);
    }
}
