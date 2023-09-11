<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Estate;

use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Repositories\Contracts\PremisesStatusRepository;
use Kelnik\EstateSearch\Models\Config;
use Kelnik\EstateSearch\Models\Contracts\Pagination;
use Kelnik\EstateSearch\Models\Contracts\SearchConfig;
use Kelnik\EstateSearch\Models\Enums\PaginationType;
use Kelnik\EstateSearch\Models\Enums\PaginationViewType;
use Kelnik\EstateSearch\Models\Filters\Area;
use Kelnik\EstateSearch\Models\Filters\Building;
use Kelnik\EstateSearch\Models\Filters\Floor;
use Kelnik\EstateSearch\Models\Filters\Price;
use Kelnik\EstateSearch\Models\Filters\Type;
use Kelnik\EstateSearch\Models\Orders\AreaTotal;
use Kelnik\EstateSearch\Models\Orders\PriceTotal;
use Kelnik\EstateSearch\View\Components\Search\Search;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\PremisesTypeGroupRepository;

trait EstatePremisesTrait
{
    private function getLivingTypeId(): ?int
    {
        return resolve(PremisesTypeGroupRepository::class)
            ->getListWithTypes()
            ->first(static fn(PremisesTypeGroup $el) => $el->living)
            ?->getKey();
    }

    private function getStatusesWhenCardAvailable(): array
    {
        return resolve(PremisesStatusRepository::class)->getListWithCardAvailable()->pluck('id')->toArray();
    }

    protected function makeConfig(?PaginationType $paginationType = null): SearchConfig
    {
        $paginationType ??= PaginationType::Frontend;

        return new Config([
            'types' => [$this->getLivingTypeId()],
            'statuses' => resolve(PremisesStatusRepository::class)
                ->getListWithCardAvailable()
                ->pluck('id')
                ->toArray(),
            'filters' => [
                ['class' => Building::class],
                ['class' => Type::class],
                ['class' => Price::class],
                ['class' => Area::class],
                ['class' => Floor::class]
            ],
            'orders' => [
                ['class' => PriceTotal::class],
                ['class' => AreaTotal::class]
            ],
            'popup' => null,
            'view' => Search::VIEW_TYPE_CARD,
            'switch' => true,
            'pagination' => resolve(Pagination::class, [
                'type' => $paginationType,
                'viewType' => PaginationViewType::Both,
                'perPage' => SearchConfig::PAGINATION_PER_PAGE_DEFAULT,
            ])
        ]);
    }
}
