<?php

declare(strict_types=1);

namespace Kelnik\Core\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Kelnik\Core\Models\Host;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Repositories\Contracts\BaseEloquentRepository;
use Kelnik\Core\Repositories\Contracts\SiteRepository;

final class SiteEloquentRepository extends BaseEloquentRepository implements SiteRepository
{
    /** @var Site $model */
    protected $model = Site::class;

    public function findByPrimaryKey(int|string $primary): Site
    {
        return $this->model::with('hosts')->findOrNew($primary);
    }

    public function findPrimary(): Site
    {
        return $this->model::query()->where('active', true)->orderByDesc('primary')->firstOrNew();
    }

    public function getAdminList(): LengthAwarePaginator
    {
        return $this->model::query()->with('hosts')->paginate();
    }

    public function getList(): LengthAwarePaginator
    {
        return $this->model::paginate();
    }

    public function getAll(): Collection
    {
        return $this->model::with('hosts')->get();
    }

    public function save(Site $model, array $hosts = []): bool
    {
        $res = $model->isDirty() ? $model->save() : $model->touch();

        if (!$res) {
            return $res;
        }

        if (!$hosts) {
            $model->hosts()->delete();

            return $res;
        }

        $curHosts = $model->hosts()->get();
        $deleteIds = [];

        /**
         * @var Host $v
         * @var Host $curVal
         */
        foreach ($curHosts as $curVal) {
            foreach ($hosts as $k => $v) {
                if ($v->value === $curVal->value) {
                    unset($hosts[$k]);
                    continue 2;
                }
            }
            $deleteIds[] = $curVal->getKey();
        }

        if ($deleteIds) {
            $model->hosts()->whereKey($deleteIds)->delete();
        }

        if ($hosts) {
            $model->hosts()->saveMany($hosts);
        }

        return $res;
    }

    public function delete(Site $model): bool
    {
        return $model->delete();
    }

    public function getActive(): Collection
    {
        return $this->model::query()
            ->select(['id', 'active', 'primary', 'type', 'locale', 'title', 'settings'])
            ->where('active', true)
            ->with('hosts')
            ->get()
            ->keyBy('id');
    }

    public function hasSite(): bool
    {
        return Schema::hasTable((new Site())->getTable());
    }
}
