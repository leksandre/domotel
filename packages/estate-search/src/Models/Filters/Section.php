<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters;

use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Filters\Contracts\AbstractCheckboxFilter;

final class Section extends AbstractCheckboxFilter
{
    public const NAME = 'section';
    protected const FILTER_FIELD = 'section_id';
    protected const TITLE = 'kelnik-estate-search::front.form.section.title';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.filters.section';

    public function getResult(Collection $dataFilter): ?Collection
    {
        $res = parent::getResult($dataFilter);
        $values = $this->repository->getSectionValues(
            $dataFilter->filter(static fn(array $param) => $param[0] !== self::FILTER_FIELD)
        );

        if (!$values) {
            return $res;
        }

        $newValues = [];

        /** @var \Kelnik\Estate\Models\Section $section */
        foreach ($values as $section) {
            if (is_numeric($section->title)) {
                $section->title = trans(self::TITLE) . ' ' . $section->title;
            }
            $newValues[$section->getKey()] = $section->toArray();
        }

        $res->put('values', $newValues);
        unset($values, $newValues);

        return $res;
    }
}
