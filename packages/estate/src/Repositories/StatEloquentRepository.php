<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Models\PremisesType;
use Kelnik\Estate\Models\Section;
use Kelnik\Estate\Models\Stat;
use Kelnik\Estate\Repositories\Contracts\StatRepository;

final class StatEloquentRepository implements StatRepository
{
    protected string $modelNamespace = Stat::class;

    /**
     * @param string $modelName
     * @param int[]|string[] $premisesTypes
     *
     * @return Collection
     */
    public function getMinPriceByModuleAndPremisesTypes(string $modelName, array $premisesTypes): Collection
    {
        return $this->modelNamespace::select([
                'model_row_id',
                'premises_type_id',
                'model_name',
                'name',
                'value'
            ])
            ->where('model_name', $modelName)
            ->whereIn('premises_type_id', $premisesTypes)
            ->where('name', 'price_min')
            ->get();
    }

    public function getStatByTypes(array $typeIds): array
    {
        $dbData = $this->modelNamespace::select(['premises_type_id', 'value', 'name'])
            ->where('model_name', '=', Complex::class)
            ->whereIn('premises_type_id', $typeIds)
            ->whereIn('name', ['price_min', 'area_min'])
            ->get();

        $res = [];

        if ($dbData->isEmpty()) {
            return $res;
        }

        foreach ($dbData as $el) {
            $val = (float) $el->value;

            $res[$el->premises_type_id][$el->name] = min(
                Arr::get($res, $el->premises_type_id . '.' . $el->name, PHP_INT_MAX),
                $val
            );
        }

        return $res;
    }

    public function getObjectsStat(int|string $complexId, array $statuses, array $modelNames): array
    {
        $premises = (new Premises());
        $tablePrefix = $premises->getConnection()->getQueryGrammar()->getTablePrefix();
        $premisesTable = (new Premises())->getTable();
        $floorTable = (new Floor())->getTable();
        $sectionTable = (new Section())->getTable();
        $buildingTable = (new Building())->getTable();

        $query = DB::table($premisesTable, 'p')
            ->select([
                'fl.id as floor',
                's.id as section',
                'b.id as building',
                'b.complex_id as complex',
                'p.type_id',
                DB::raw('COUNT(`' . $tablePrefix . 'p`.`id`) as cnt'),
                DB::raw(
                    'MIN(' .
                    'CASE ' .
                        'WHEN (`' . $tablePrefix . 'p`.`price_total` > 0) THEN `' . $tablePrefix . 'p`.`price_total` ' .
                        'ELSE `' . $tablePrefix . 'p`.`price` ' .
                    'END' .
                    ') as price_min'
                ),
                DB::raw(
                    'MAX(' .
                    'CASE ' .
                        'WHEN (`' . $tablePrefix . 'p`.`price_total` > 0) THEN `' . $tablePrefix . 'p`.`price_total` ' .
                        'ELSE `' . $tablePrefix . 'p`.`price` ' .
                    'END' .
                    ') as price_max'
                ),
                DB::raw('MAX(`' . $tablePrefix . 'p`.`area_total`) area_max'),
                DB::raw('MIN(`' . $tablePrefix . 'p`.`area_total`) area_min')
            ])
            ->join($floorTable . ' as fl', 'fl.id', '=', 'p.floor_id')
            ->where('fl.active', true)
            ->join($sectionTable . ' as s', 's.id', '=', 'p.section_id', 'left')
            ->where('s.active', true)
            ->join($buildingTable . ' as b', 'b.id', '=', 'fl.building_id')
            ->where('b.active', true)
            ->whereIn('p.status_id', $statuses)
            ->where('p.active', true)
            ->groupBy(['fl.id', 'p.type_id']);

        if ($complexId) {
            $query->where(`' . $tablePrefix . 'b` . `complex_id`, $complexId);
        }

        $data = [];

        try {
            $fields = ['cnt', 'price_min', 'price_max', 'area_min', 'area_max'];

            foreach ($query->cursor() as $row) {
                $row = (array)$row;
                foreach ($modelNames as $model) {
                    $curRow = &$data[$row['type_id']][$model][$row[$model]];
                    foreach ($fields as $fieldName) {
                        $method = 'add';

                        if (stripos($fieldName, '_min')) {
                            $method = 'min';
                        } elseif (stripos($fieldName, '_max')) {
                            $method = 'max';
                        }

                        $curRow[$fieldName] = isset($curRow[$fieldName])
                            ? $method === 'add'
                                ? $curRow[$fieldName] + $row[$fieldName]
                                : call_user_func($method, $curRow[$fieldName], $row[$fieldName])
                            : $row[$fieldName];

                        if ($model !== 'floor') {
                            $curRow['floor_min'] = isset($curRow['floor_min']) ? min(
                                $curRow['floor_min'],
                                $row['floor']
                            ) : $row['floor'];
                            $curRow['floor_max'] = isset($curRow['floor_max']) ? max(
                                $curRow['floor_max'],
                                $row['floor']
                            ) : $row['floor'];
                        }
                    }
                }
            }
            unset($curRow);
        } catch (Exception $e) {
            return $data;
        }

        return $data;
    }

    public function getEdgePrices(array $modelNames): array
    {
        return $this->modelNamespace::query()
            ->select(['model_name', 'value', 'name'])
            ->whereIn('model_name', $modelNames)
            ->whereIn('name', ['price_min', 'price_max'])
            ->whereIn(
                'premises_type_id',
                PremisesType::query()
                    ->select(['id'])
                    ->whereHas('typeGroup', function (Builder $builder) {
                        $builder->select('id')->where('living', true)->limit(1);
                    })
            )
            ->get()
            ->toArray();
    }

    public function updateStat(array $models, array $data): void
    {
        $this->modelNamespace::query()->delete();

        foreach ($data as $premisesTypeId => $premisesStat) {
            foreach ($models as $keyName => $model) {
                if (!isset($premisesStat[$keyName])) {
                    continue;
                }

                $rows = [];

                foreach ($premisesStat[$keyName] as $modelKey => $modelData) {
                    foreach ($modelData as $name => $value) {
                        $rows[] = [
                            'premises_type_id' => $premisesTypeId,
                            'model_row_id' => $modelKey,
                            'model_name' => $model,
                            'name' => $name,
                            'value' => $value,
                            'created_at' => now()
                        ];
                    }
                }

                if (!$rows) {
                    continue;
                }

                DB::transaction(function () use ($rows): void {
                    $this->modelNamespace::query()->insert($rows);
                });
            }
        }
    }
}
