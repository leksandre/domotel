<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\EstateSearch\Models\Filters\Contracts\AbstractCheckboxFilter;
use Kelnik\EstateSearch\Providers\EstateSearchServiceProvider;
use Kelnik\EstateSearch\Services\Contracts\SearchService;

final class Type extends AbstractCheckboxFilter
{
    public const NAME = 'stype';
    public const ROOM_COUNT_LIMIT = 4;
    protected const FILTER_FIELD = 'type_id';
    protected const TITLE = 'kelnik-estate-search::front.form.type.title';
    protected const ADMIN_TITLE = 'kelnik-estate-search::admin.form.filters.type';

    public function getViewType(): string
    {
        return self::VIEW_TYPE_BLOCK;
    }

    public function getResult(Collection $dataFilter): ?Collection
    {
        $res = parent::getResult($dataFilter);
        $values = $this->repository->getTypeValues(
            $dataFilter->filter(static fn(array $param) => $param[0] !== self::FILTER_FIELD)
        )?->pluck(null, 'id')?->toArray();

        if (!$values || !$this->useLimit()) {
            $res->put('values', $values);
            return $res;
        }

        $hasLimit = false;

        foreach ($values as $k => &$val) {
            if ($val['rooms'] === self::ROOM_COUNT_LIMIT) {
                $this->modifyVariant($val);
                $hasLimit = true;
                continue;
            }
            if ($val['rooms'] > self::ROOM_COUNT_LIMIT) {
                if (!$hasLimit) {
                    $this->modifyVariant($val);
                    $hasLimit = true;
                    continue;
                }
                unset($values[$k]);
            }
        }

        $res->put('values', $values ?? []);

        return $res;
    }

    private function modifyVariant(array &$val): void
    {
        $val['title'] .= '+';
        $val['short_title'] .= '+';
    }

    private function useLimit(): bool
    {
        return true;
    }

    public function getDataFilterParams(): Collection
    {
        $values = Arr::get($this->requestValues, self::NAME, []);
        $values = array_map('intval', $values);
        $values = array_filter($values);

        if (!$values) {
            return new Collection();
        }

        if ($this->useLimit()) {
            $variants = $this->getAllVariants($this->additionalValues[SearchService::PARAM_TYPES]);

            if ($variants) {
                $hasLimit = false;
                foreach ($variants as $k => $rooms) {
                    $isLimitedRange = $rooms >= self::ROOM_COUNT_LIMIT;
                    if (in_array($k, $values) && $isLimitedRange) {
                        $hasLimit = true;
                    }

                    if ($hasLimit && $isLimitedRange) {
                        $values[$k] = $k;
                    }
                }
            }
        }

        return new Collection([
            [self::FILTER_FIELD, 'in', $values]
        ]);
    }

    private function getAllVariants(array $typeGroupIds): array
    {
        $cacheId = 'estateSearch_filter_type_variants';
        $res = Cache::get($cacheId);

        if ($res) {
            return $res;
        }

        $res = $this->repository->getTypesByGroup($typeGroupIds)->pluck('rooms', 'id')->toArray();

        Cache::tags([
            resolve(EstateService::class)->getModuleCacheTag(),
            EstateSearchServiceProvider::MODULE_NAME
        ])->forever($cacheId, $res);

        return $res;
    }
}
