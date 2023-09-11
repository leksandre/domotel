<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Filters\Contracts\AbstractToggleFilter;

final class PriceSale extends AbstractToggleFilter
{
    public const NAME = 'action';
    protected const FILTER_FIELD = 'price_sale';
    protected const TITLE = 'kelnik-estate-search::front.form.priceSale.title';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.filters.priceSale';

    public function getResult(Collection $dataFilter): ?Collection
    {
        $res = parent::getResult($dataFilter);
        $dataFilter->add($this->getFilter());
        $hasPriceSale = $this->repository->hasPremisesByFilter($dataFilter);

        $res->put(
            'values',
            $hasPriceSale ? ['id' => self::VALUE] : []
        );

        return $res;
    }

    public function getDataFilterParams(): Collection
    {
        $value = (int)Arr::get($this->requestValues, self::NAME, []);
        $res = new Collection();

        if ($value) {
            $res->add($this->getFilter());
        }

        return $res;
    }

    private function getFilter(): array
    {
        return [self::FILTER_FIELD, '>', 0.0];
    }
}
