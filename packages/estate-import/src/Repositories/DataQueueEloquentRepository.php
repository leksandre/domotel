<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Kelnik\EstateImport\Models\DataQueue;
use Kelnik\EstateImport\Models\Enums\DataQueueEvent;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Repositories\Contracts\DataQueueRepository;

final class DataQueueEloquentRepository implements DataQueueRepository
{
    protected string $modelNamespace = DataQueue::class;

    public function saveMulti(array $rows): int
    {
        if (!$rows) {
            return 0;
        }

        $rows = array_map(function (DataQueue|array $row) {
            if ($row instanceof $this->modelNamespace) {
                $row = $row->toArray();
            }
            $row['data'] = json_encode($row['data']);

            return $row;
        }, $rows);

        return $this->modelNamespace::upsert(
            $rows,
            ['id'],
            array_keys(current($rows ?? []))
        );
    }

    public function save(DataQueue $dataQueue): bool
    {
        return $dataQueue->save();
    }

    public function getLazyCollection(History $history): LazyCollection
    {
        return $this->modelNamespace::where('history_id', $history->getKey())->where('done', false)->cursor();
    }

    public function getStatByHistory(History $history): Collection
    {
        return $this->modelNamespace::select([
                'model',
                DB::raw('SUM(`event` = ' . DataQueueEvent::Added->value . ') as `added`'),
                DB::raw('SUM(`event` = ' . DataQueueEvent::Updated->value . ') as `updated`'),
            ])
            ->where('history_id', $history->getKey())
            ->groupBy('model')
            ->withCasts([
                'added' => 'integer',
                'updated' => 'integer'
            ])
            ->get();
    }

    public function forceDeleteByHistory(History $history): bool
    {
        $className = $this->modelNamespace;
        $tableName = (new $className())->getTable();

        return DB::table($tableName)->where('history_id', $history->getKey())->delete() > 0;
    }
}
