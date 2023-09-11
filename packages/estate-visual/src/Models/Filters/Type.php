<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Filters;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\EstateVisual\Models\Filters\Contracts\AbstractCheckboxFilter;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\PremisesStatusRepository;

final class Type extends AbstractCheckboxFilter
{
    public const NAME = 'room';
    protected const FILTER_FIELD = 'type_id';

    public function getTitle(): string
    {
        return trans('kelnik-estate-visual::front.form.room.title');
    }

    public function getResult(Collection $dataFilter): ?Collection
    {
        /** @var Collection $statuses */
        $res = parent::getResult($dataFilter);
        $colors = $this->selectorSettings->get('colors');
        $activeStatuses = $this->additionalValues[self::PARAM_STATUSES] ?? [];
        $statuses = resolve(PremisesStatusRepository::class)
            ->getList()
            ->sortByDesc(static fn(PremisesStatus $el) => $el->premises_card_available);

        if ($activeStatuses) {
            $statuses = $statuses->filter(static fn(PremisesStatus $el) => in_array($el->getKey(), $activeStatuses));
        }
        $status = $statuses->first()?->getKey();
        $values = $this->repository->getRoomValues(
            $dataFilter->filter(static fn(array $param) => $param[0] !== self::FILTER_FIELD)
        )?->pluck(null, 'id')?->toArray() ?? [];

        $values = array_map(static function (array $el) use ($colors, $status) {
            $el['color'] = Arr::get(
                $colors,
                $el['id'] . '.' . $status,
                current($colors[$el['id']] ?? []) ?: null
            );

            return $el;
        }, $values);

        $res->put('values', $values);

        return $res;
    }
}
