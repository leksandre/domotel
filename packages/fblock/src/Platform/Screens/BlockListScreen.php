<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Platform\Screens;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Kelnik\FBlock\Platform\Layouts\Block\ListLayout;
use Kelnik\FBlock\Repositories\Contracts\BlockRepository;
use Orchid\Screen\Actions\Link;

class BlockListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-fblock::admin.menu.elements');

        return [
            'list' => resolve(BlockRepository::class)->getAll(),
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('fblock.sort'),
                [],
                false
            ),
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-fblock::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName('fblock.element'))
        ];
    }

    public function layout(): array
    {
        return [
            ListLayout::class
        ];
    }

    public function sortable(Request $request): JsonResponse
    {
        $fields = array_values($request->input('elements', []));

        if (!$fields) {
            return Response::json([
                'success' => false,
                'messages' => [trans('kelnik-fblock::admin.error.emptyList')]
            ]);
        }

        foreach ($fields as $k => &$v) {
            $v = (int) $v;
            if (!$v) {
                unset($fields[$k]);
            }
        }

        $this->blockPlatformService->sortElements($fields);

        return Response::json([
            'success' => true,
            'messages' => [trans('kelnik-fblock::admin.success')]
        ]);
    }
}
