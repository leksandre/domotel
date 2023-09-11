<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Mortgage\Models\Bank;
use Kelnik\Mortgage\Repositories\Contracts\BankRepository;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Field;
use Orchid\Support\Facades\Toast;

final class MortgagePlatformService implements Contracts\MortgagePlatformService
{
    public function __construct(
        private BankRepository $bankRepository,
        private CoreService $coreService
    ) {
    }

    public function saveBank(Bank $bank, Request $request): RedirectResponse
    {
        $bankData = $request->only([
            'bank.title',
            'bank.active',
            'bank.link',
            'bank.priority',
            'bank.description',
            'bank.logo_id'
        ]);

        $programs = $request->input(['programs'], []);

        if ($programs) {
            $request->validate([
                'programs.*.min_time' => 'numeric',
                'programs.*.max_time' => 'numeric',
                'programs.*.min_payment_percent' => 'numeric',
                'programs.*.max_payment_percent' => 'numeric',
                'programs.*.rate' => 'numeric',
            ]);
        }

        $bankData = Arr::get($bankData, 'bank');
        if (!$bank->exists) {
            $bankData['priority'] = $this->bankRepository->getMaxPriority() + 1;
        }
        $bank->fill($bankData);

        $this->bankRepository->save($bank, $programs);
        Toast::info(trans('kelnik-mortgage::admin.saved'));

        return redirect()->route(
            $this->coreService->getFullRouteName('mortgage.banks')
        );
    }

    public function sortBanks(array $banksPriority): bool
    {
        /** @var BankRepository $banksRepo */
        $banksRepo = resolve(BankRepository::class);
        $banks = $banksRepo->getAll();

        if ($banks->isEmpty()) {
            return false;
        }

        $banks->each(static function (Bank $el) use ($banksPriority, $banksRepo) {
            $el->priority = (int)array_search($el->getKey(), $banksPriority) + Bank::PRIORITY_DEFAULT;
            $banksRepo->save($el);
        });

        return true;
    }

    public function getContentLink(): Field
    {
        return Link::make(trans('kelnik-mortgage::admin.banksList'))
            ->route($this->coreService->getFullRouteName('mortgage.banks'))
            ->icon('info')
            ->class('btn btn-info')
            ->target('_blank')
            ->style('display: inline-block; margin-bottom: 20px');
    }
}
