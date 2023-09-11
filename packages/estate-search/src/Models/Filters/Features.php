<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters;

use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Filters\Contracts\AbstractCheckboxFilter;

final class Features extends AbstractCheckboxFilter
{
    public const NAME = 'option';
    protected const FILTER_FIELD = 'features.feature_id';
    protected const TITLE = 'kelnik-estate-search::front.form.feature.title';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.filters.features';

    public function getResult(Collection $dataFilter): ?Collection
    {
        $res = parent::getResult($dataFilter);

        $res->put(
            'values',
            $this->repository->getFeatureValues(
                $dataFilter->filter(static fn(array $param) => $param[0] !== self::FILTER_FIELD)
            )?->pluck(null, 'id')?->map(function ($value) {
                $value = $value->toArray();
                $value['title'] = $value['full_title'];
                unset($value['full_title']);

                return $value;
            })?->toArray() ?? []
        );

        return $res;
    }
}
