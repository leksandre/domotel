<?php

declare(strict_types=1);

namespace Kelnik\Document\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Kelnik\Document\Models\Category;
use Kelnik\Document\Models\Element;
use Kelnik\Document\Repositories\Contracts\CategoryRepository;

final class CategoryEloquentRepository implements CategoryRepository
{
    /** @var class-string<Category> $model */
    private $model = Category::class;

    public function isUnique(Model $model): bool
    {
        $query = $this->model::query()->where('slug', '=', $model->slug)->limit(1);

        if ($model->exists) {
            $query->whereKeyNot($model->id);
        }

        return $query->get('id')->count() === 0;
    }

    public function findByPrimary(int|string $primary): Category
    {
        return $this->model::findOrNew($primary);
    }

    public function getAll(): Collection
    {
        return $this->model::query()->orderBy('priority')->orderBy('title')->get();
    }

    public function getAdminList(): LengthAwarePaginator
    {
        return $this->model::filters()->defaultSort('priority')->withCount('elements')->with('group')->paginate();
    }

    public function getActiveWithElements(?int $group = null): Collection
    {
        return $this->model::query()
            ->select(['id', 'slug', 'title'])
            ->with([
                'elements' => static function (HasMany $elementQuery) {
                    $elementQuery->select(['id', 'category_id', 'attachment_id', 'publish_date', 'title', 'author'])
                        ->with('attachment')
                        ->where('active', true)
                        ->whereHas('attachment', static fn(Builder $builder) => $builder->select('id')->limit(1));
                }
            ])
            ->when(
                $group,
                static fn(Builder $builder) => $builder->whereHas(
                    'group',
                    static fn(Builder $query) => $query->select('id')->active()->whereKey($group)
                )
            )
            ->where('active', true)
            ->orderBy('priority')
            ->get();
    }

    /**
     * @param Category $model
     * @param array|null $elements
     *
     * @return bool
     */
    public function save(Model $model, ?array $elements = null): bool
    {
        $res = $model->save();

        if (!$res) {
            return $res;
        }

        if (!$elements) {
            if (is_array($elements)) {
                $model->elements()->get()->each->delete();
            }

            return $res;
        }

        $newElements = new Collection(array_values($elements));

        $model->elements->each(static function (Element $el) use (&$newElements) {
            $elIndex = 0;
            $elFromRequest = $newElements->first(static function ($newElement, $key) use ($el, &$elIndex) {
                $elIndex = $key;
                return (int)($newElement['id'] ?? 0) === $el->getKey();
            });

            if (!$elFromRequest) {
                $el->delete();
                return;
            }

            $elFromRequest['priority'] = Element::PRIORITY_DEFAULT + $elIndex;

            unset($elFromRequest['id']);
            $newElements->forget($elIndex);

            $el->fill($elFromRequest)->save();
        });

        if ($newElements) {
            foreach ($newElements as $index => $el) {
                $el['priority'] = Element::PRIORITY_DEFAULT + (int)$index;
                unset($el['id']);
                (new Element($el))->category()->associate($model)->save();
            }
        }

        return $res;
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
