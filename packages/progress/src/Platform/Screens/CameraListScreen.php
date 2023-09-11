<?php

declare(strict_types=1);

namespace Kelnik\Progress\Platform\Screens;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Kelnik\Progress\Http\Requests\ElementSortRequest;
use Kelnik\Progress\Platform\Layouts\Camera\ListLayout;
use Orchid\Screen\Actions\Link;

final class CameraListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-progress::admin.menu.cameras');

        return [
            'list' => $this->cameraRepository->getAdminList(),
            'sortableUrl' => route($this->coreService->getFullRouteName('progress.cameras.sort'), [], false),
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-progress::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName('progress.camera'))
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
        $this->progressService->sortCameras($request->getDto());

        return Response::json([
            'success' => true,
            'messages' => [trans('kelnik-document::admin.success')]
        ]);
    }
}
