<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\PremisesCard;

use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Repositories\Contracts\PremisesRepository;

trait LoadPremises
{
    protected function loadPremisesData(): ?Premises
    {
        $repository = resolve(PremisesRepository::class);
        $res = $repository->findByPrimaryForCard($this->primary);

        if (!$res->exists || !$res->active || !$res->status->card_available) {
            return null;
        }

        $res = $repository->loadFullCardData($res);

        if (!$res->completely_active) {
            return null;
        }

        $routeName = $this->pageLinkService->getRouteNameByElement(
            resolve(SiteService::class)->current(),
            PremisesTypeGroup::class,
            $res->type->group_id
        );

        if ($routeName !== $this->routeName) {
            return null;
        }

        return $this->estateService->preparePremises(
            new Collection([$res]),
            new Collection([$res->type->typeGroup->getKey() => $this->routeName])
        )->first();
    }
}
