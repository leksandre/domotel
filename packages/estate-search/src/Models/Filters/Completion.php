<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters;

use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Filters\Contracts\AbstractCheckboxFilter;

final class Completion extends AbstractCheckboxFilter
{
    public const NAME = 'completion';
    protected const FILTER_FIELD = 'floor.building.completion_id';
    protected const TITLE = 'kelnik-estate-search::front.form.completion.title';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.filters.completion';

    public function getResult(Collection $dataFilter): ?Collection
    {
        $res = parent::getResult($dataFilter);
        $res->put(
            'values',
            $this->repository->getCompletionValues(
                $dataFilter->filter(static fn(array $param) => $param[0] !== self::FILTER_FIELD)
            )?->pluck(null, 'id')?->toArray() ?? []
        );

        return $res;
    }
}
