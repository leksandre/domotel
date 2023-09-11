<?php

declare(strict_types=1);

namespace Kelnik\Estate\Services\Contracts;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Repositories\Contracts\PlanoplanRepository;

interface PlanoplanService
{
    public function __construct(PlanoplanRepository $repository, CoreService $coreService);

    public function getCacheTag(int|string $primary): ?string;
}
