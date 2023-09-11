<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Mortgage\Models\Bank;
use Kelnik\Mortgage\Repositories\Contracts\BankRepository;
use Orchid\Screen\Field;

interface MortgagePlatformService
{
    public function __construct(BankRepository $bankRepository, CoreService $coreService);

    public function saveBank(Bank $bank, Request $request): RedirectResponse;

    public function sortBanks(array $banksPriority): bool;

    public function getContentLink(): Field;
}
