<?php

declare(strict_types=1);

namespace Kelnik\Page\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Page\View\Components\Contracts\KelnikPageDynamicComponent;

final class KelnikBladeComponentRepository implements BladeComponentRepository
{
    public function getViewComponents(): Collection
    {
        return collect(Blade::getClassComponentAliases())->filter(
            static fn($classNamespace, $alias) => is_a($classNamespace, KelnikPageComponent::class, true)
        );
    }

    public function getDynamicComponents(): Collection
    {
        return collect(Blade::getClassComponentAliases())->filter(
            static fn($classNamespace, $alias) => is_a($classNamespace, KelnikPageComponent::class, true)
                && is_a($classNamespace, KelnikPageDynamicComponent::class, true)
        );
    }

    public function getList(): Collection
    {
        return $this->getViewComponents()->map(static fn($classNamespace) => $classNamespace::initDataProvider());
    }

    public function getAdminList(): Collection
    {
        return $this->getList()
            ->sortBy(static fn(ComponentDataProvider $componentData) => $componentData->getComponentTitle())
            ->map(static fn(ComponentDataProvider $componentData) => [
                'code' => $componentData->getComponentCode(),
                'title' => $componentData->getComponentTitle()
            ])
            ->pluck('title', 'code');
    }

    public function findByPrimary(int|string $primary): ?string
    {
        /** @var ComponentDataProvider $componentDP */
        $componentDP = $this->getList()->first(
            static fn(ComponentDataProvider $componentData) => $componentData->getComponentCode() === $primary
        );

        return $componentDP?->getComponentNamespace() ?: null;
    }
}
