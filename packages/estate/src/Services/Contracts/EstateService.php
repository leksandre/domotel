<?php

declare(strict_types=1);

namespace Kelnik\Estate\Services\Contracts;

use Closure;
use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Repositories\Contracts\PremisesRepository;

interface EstateService
{
    public function __construct(PremisesRepository $premisesRepository, CoreService $coreService);

    public function preparePremises(Collection $res, Collection $cardRoutes, ?Closure $callback = null): Collection;

    public function getTypeTitle(Premises $premises): string;

    public function getShortTypeTitle(Premises $premises): string;

    public function getInternalTypeTitle(Premises $premises): string;

    public function createSlugByTitle(string $title): string;

    public function getModuleCacheTag(): string;

    public function getPremisesCacheTag(int|string $id): ?string;
}
