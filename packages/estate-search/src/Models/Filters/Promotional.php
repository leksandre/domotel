<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Filters\Contracts\AbstractToggleFilter;

final class Promotional extends AbstractToggleFilter
{
    public const NAME = 'promo';
    protected const FILTER_FIELD = 'action';
    protected const TITLE = 'kelnik-estate-search::front.form.action.title';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.filters.action';

    public function getResult(Collection $dataFilter): ?Collection
    {
        $res = parent::getResult($dataFilter);

        $hasPromotional = $this->repository->hasPromotionalPremises(
            $dataFilter->filter(static fn(array $param) => $param[0] !== self::FILTER_FIELD)
        );

        $res->put(
            'values',
            $hasPromotional ? ['id' => self::VALUE] : []
        );

        return $res;
    }

    public function getDataFilterParams(): Collection
    {
        $value = (int)Arr::get($this->requestValues, self::NAME, []);
        $res = new Collection();

        if ($value) {
            $res->add([self::FILTER_FIELD, '=', true]);
        }

        return $res;
    }
}
