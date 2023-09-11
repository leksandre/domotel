<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters;

use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Filters\Contracts\AbstractCheckboxFilter;

final class Status extends AbstractCheckboxFilter
{
    public const NAME = 'state';
    protected const FILTER_FIELD = 'status_id';
    protected const TITLE = 'kelnik-estate-search::front.form.state.title';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.filters.state';

    public function getResult(Collection $dataFilter): ?Collection
    {
        $res = parent::getResult($dataFilter);

        $res->put(
            'values',
            $this->repository->getStatusValues(
                $dataFilter->filter(static fn(array $param) => $param[0] !== self::FILTER_FIELD),
                $this->additionalValues['statuses'] ?? []
            )?->pluck(null, 'id')?->toArray() ?? []
        );

        return $res;
    }
}
