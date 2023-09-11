<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Services\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Mortgage\Repositories\Contracts\BankRepository;

interface MortgageService
{
    public function __construct(BankRepository $bankRepository, CoreService $coreService);

    public function getBanksListWithSummary(array $banksIds = [], bool $programParamsRange = false): Collection;

    public function getBankCacheTag(int|string $id): ?string;

    public function getBankListCacheTag(): string;
}
