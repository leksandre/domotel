<?php

declare(strict_types=1);

namespace Kelnik\Estate\Services;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Repositories\Contracts\PlanoplanRepository;

final class PlanoplanService implements Contracts\PlanoplanService
{
    public function __construct(
        private readonly PlanoplanRepository $repository,
        private readonly CoreService $coreService
    ) {
    }

    public function getCacheTag(int|string $primary): ?string
    {
        return 'estate_planoplan_' . $primary;
    }
}
