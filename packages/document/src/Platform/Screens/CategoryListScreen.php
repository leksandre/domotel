<?php

declare(strict_types=1);

namespace Kelnik\Document\Platform\Screens;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Kelnik\Document\Http\Requests\ElementSortRequest;
use Kelnik\Document\Platform\Layouts\Category\ListLayout;
use Orchid\Screen\Actions\Link;

final class CategoryListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-document::admin.menu.categories');

        return [
            'list' => $this->categoryRepository->getAdminList(),
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('document.category.sort'),
                [],
                false
            ),
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-document::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName('document.category'))
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
        $this->documentService->sortCategories($request->getDto());

        return Response::json([
            'success' => true,
            'messages' => [trans('kelnik-document::admin.success')]
        ]);
    }
}
