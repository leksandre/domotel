<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kelnik\Mortgage\Models\Bank;
use Kelnik\Mortgage\Models\Program;
use Kelnik\Mortgage\Repositories\Contracts\BankRepository;

final class BankEloquentRepository implements BankRepository
{
    /** @var class-string $model */
    private string $model = Bank::class;

    public function getAll(): Collection
    {
        return $this->model::query()->withCount('programs')->orderBy('priority')->get();
    }

    public function getMaxPriority(): int
    {
        return $this->model::query()->max('priority') ?? Bank::PRIORITY_DEFAULT;
    }

    public function getActiveWithPrograms(array $bankIds = []): Collection
    {
        $query = $this->model::query()->where('active', true);

        if ($bankIds) {
            $query->whereIn('id', $bankIds);
        }

        return $query
            ->whereHas(
                'programs',
                static fn(Builder $query) => $query
                    ->select(['id'])
                    ->where('active', true)
                    ->limit(1)
            )
            ->with(['programs', 'logo'])
            ->orderBy('priority')
            ->orderBy('title')
            ->get();
    }

    public function findByPrimary(int|string $primary): Model
    {
        return $this->model::findOrNew($primary);
    }

    public function save(Model $model, ?array $newPrograms = null): bool
    {
        $res = $model->save();

        if (!$res) {
            return $res;
        }

        if (!$newPrograms) {
            if (is_array($newPrograms)) {
                $model->programs()->delete();
            }

            return $res;
        }

        $newPrograms = new Collection(array_values($newPrograms));

        $model->programs->each(static function (Program $el) use (&$newPrograms) {
            $programIndex = 0;
            $programFromRequest = $newPrograms->first(static function ($program, $key) use ($el, &$programIndex) {
                $programIndex = $key;
                return (int)($program['id'] ?? 0) === $el->id;
            });

            if (!$programFromRequest) {
                $el->delete();
                return;
            }

            $programFromRequest['priority'] = Program::PRIORITY_DEFAULT + $programIndex;

            unset($programFromRequest['id']);
            $newPrograms->forget($programIndex);

            $el->fill($programFromRequest)->save();
        });

        if ($newPrograms) {
            foreach ($newPrograms as $index => $el) {
                $el['priority'] = Program::PRIORITY_DEFAULT + (int)$index;
                unset($el['id']);
                (new Program($el))->bank()->associate($model)->save();
            }
        }

        return $res;
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
