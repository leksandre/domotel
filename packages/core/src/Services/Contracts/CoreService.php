<?php

declare(strict_types=1);

namespace Kelnik\Core\Services\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Core\Dto\ClearingDto;
use Kelnik\Core\Enums\ClearingResultType;

interface CoreService
{
    public function getFullRouteName(string $routeName): string;

    /** @return Collection<\Kelnik\Core\Models\Contracts\KelnikModuleInfo> */
    public function getModuleList(): Collection;

    public function hasModule(string $moduleName): bool;

    public function getModuleNameByClassNamespace(string $classNamespace): string;

    public function clearingModules(ClearingDto $dto): ClearingResultType;
}
