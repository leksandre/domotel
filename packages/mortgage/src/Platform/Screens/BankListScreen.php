<?php

namespace Kelnik\Mortgage\Platform\Screens;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Kelnik\Mortgage\Platform\Layouts\Bank\ListLayout;
use Kelnik\Mortgage\Repositories\Contracts\BankRepository;
use Orchid\Screen\Actions\Link;

final class BankListScreen extends Screen
{
    public function query(): array
    {
        $this->name = trans('kelnik-mortgage::admin.menu.banks');

        return [
            'list' => resolve(BankRepository::class)->getAll(),
            'sortableUrl' => route($this->coreService->getFullRouteName('mortgage.banks.sort'), [], false),
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-mortgage::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName('mortgage.bank'))
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
        $banks = array_values($request->input('elements', []));

        if (!$banks) {
            return Response::json([
                'success' => false,
                'messages' => [trans('kelnik-mortgage::admin.error.emptyList')]
            ]);
        }

        foreach ($banks as $k => &$v) {
            $v = (int) $v;

            if (!$v) {
                unset($banks[$k]);
            }
        }

        $this->mortgagePlatformService->sortBanks($banks);

        return Response::json([
            'success' => true,
            'messages' => [trans('kelnik-mortgage::admin.success')]
        ]);
    }
}
