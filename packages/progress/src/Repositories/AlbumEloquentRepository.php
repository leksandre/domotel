<?php

declare(strict_types=1);

namespace Kelnik\Progress\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kelnik\Progress\Models\Album;
use Kelnik\Progress\Models\AlbumVideo;
use Kelnik\Progress\Repositories\Contracts\AlbumRepository;

final class AlbumEloquentRepository implements AlbumRepository
{
    private string $model = Album::class;

    public function getAdminList(): LengthAwarePaginator
    {
        return $this->model::withCount(['images', 'videos'])
            ->with('group')
            ->orderByDesc('publish_date')
            ->paginate();
    }

    public function getActive(?int $group = null): Collection
    {
        return $this->model::with(['images', 'videos'])
            ->where(
                static fn(Builder $builder) => $builder
                    ->whereHas('images', static fn(Builder $query) => $query->limit(1))
                    ->orWhereHas('videos', static fn(Builder $query) => $query->select(['id'])->limit(1))
            )
            ->when(
                $group,
                static fn(Builder $builder) => $builder->whereHas(
                    'group',
                    static fn(Builder $query) => $query->select('id')->active()->whereKey($group)
                )
            )
            ->active()
            ->orderByDesc('publish_date')
            ->orderByDesc('id')
            ->get();
    }

    public function findByPrimary(int|string $primary): Model
    {
        return $this->model::findOrNew($primary);
    }

    public function findByPrimaryWithImagesAndVideos(int|string $primary): Album
    {
        return $this->model::whereKey($primary)->with(['images', 'videos'])->firstOrNew();
    }

    public function save(Model $model, array $imageIds = [], ?array $videos = null): bool
    {
        $res = $model->save();

        if (!$res) {
            return $res;
        }

        $model->images()->syncWithoutDetaching($imageIds);

        if (!$videos) {
            if (is_array($videos)) {
                $model->videos()->get()->each->delete();
            }

            return $res;
        }

        $videos = new Collection(array_values($videos));

        $model->videos->each(static function (AlbumVideo $el) use (&$videos) {
            $videoIndex = 0;
            $videoFromRequest = $videos->first(static function ($video, $key) use ($el, &$videoIndex) {
                $videoIndex = $key;
                return (int)($video['id'] ?? 0) === $el->id;
            });

            if (!$videoFromRequest) {
                $el->delete();
                return;
            }

            $videoFromRequest['priority'] = AlbumVideo::PRIORITY_DEFAULT + $videoIndex;
            $videos->forget($videoIndex);
            unset($videoFromRequest['id']);

            $el->fill($videoFromRequest)->save();
        });

        if ($videos) {
            foreach ($videos as $index => $el) {
                $el['priority'] = AlbumVideo::PRIORITY_DEFAULT + (int)$index;
                unset($el['id']);
                (new AlbumVideo($el))->album()->associate($model)->save();
            }
        }

        return $res;
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
