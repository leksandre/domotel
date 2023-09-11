<?php

declare(strict_types=1);

namespace Kelnik\Menu\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Menu\Models\Menu;
use Kelnik\Menu\Models\MenuItem;
use Kelnik\Menu\Repositories\Contracts\MenuRepository;

final class MenuEloquentRepository implements MenuRepository
{
    /** @var class-string $model */
    private $model = Menu::class;

    public function findByPrimary(int|string $primary): Menu
    {
        return $this->model::findOrNew($primary);
    }

    public function findActiveByPrimary(int|string $primary): Menu
    {
        return $this->model::where('active', true)->findOrNew($primary);
    }

    public function getAll(): Collection
    {
        return $this->model::query()->orderBy('title')->get();
    }

    public function getAdminList(): LengthAwarePaginator
    {
        return $this->model::filters()->defaultSort('id')->withCount('items')->paginate();
    }

    public function getElements(Menu $menu): Collection
    {
        return $menu->items()
            ->active()
            ->with([
                'icon',
                'page' => static fn (BelongsTo $query) => $query->fieldsForFront(),
                'pageComponent' => static fn (BelongsTo $query) => $query->fieldsForFront()
            ])
            ->get();
    }

    public function findByPageOrPageComponent(int|string $pageKey, int|string $pageComponentKey): Collection
    {
        if (!$pageKey && !$pageComponentKey) {
            return new Collection();
        }

        $query = MenuItem::query()->select(['id', 'menu_id']);

        if ($pageKey) {
            $query->where('page_id', $pageKey);
        }

        if ($pageComponentKey) {
            $query->where('page_component_id', $pageComponentKey);
        }

        return $query->get();
    }

    /**
     * @param Menu $model
     * @param array|null $items
     *
     * @return bool
     */
    public function save(Model $model, ?array $items = null): bool
    {
        $res = $model->save();

        if (!$res) {
            return $res;
        }

        if (!$items && is_array($items)) {
            $model->items()->get()->each->delete();

            return $res;
        }

        $items = new Collection(array_values($items));
        $promises = [];
        $curItems = $model->items;
        $curIds = $curItems->pluck('id')->toArray();
        $curItems = $curItems->each(static function (MenuItem &$el) use (&$items, &$promises, $curIds) {
            $itemIndex = 0;

            $itemFromRequest = $items->first(static function (array $item, $index) use ($el, &$itemIndex) {
                $itemIndex = $index;
                return (int)($item['id'] ?? MenuItem::DEFAULT_INT_VALUE) === $el->id;
            });

            if (!$itemFromRequest) {
                $el->delete();

                return;
            }

            $itemFromRequest['priority'] = MenuItem::PRIORITY_DEFAULT + $itemIndex;
            $itemFromRequest['active'] = !empty($itemFromRequest['active']);
            $itemFromRequest['marked'] = !empty($itemFromRequest['marked']);

            unset($itemFromRequest['id']);
            $items->forget($itemIndex);

            // Update parent_id
            $el->parent_id = (int)($itemFromRequest['parent_id'] ?? MenuItem::DEFAULT_INT_VALUE);
            $el->page_id = $itemFromRequest['page_id'] ?? MenuItem::DEFAULT_INT_VALUE;
            $el->page_component_id = $itemFromRequest['page_component_id'] ?? MenuItem::DEFAULT_INT_VALUE;

            unset($itemFromRequest['parent_id'], $itemFromRequest['page_id'], $itemFromRequest['page_component_id']);
            $el->fill($itemFromRequest);

            // Parent exists in DB
            if (!$el->hasParent() || in_array($el->parent_id, $curIds)) {
                $el->save();

                return;
            }

            // Searching new parent value
            $parent = $items->first(static function (array $item) use ($el) {
                return (int)($item['id'] ?? MenuItem::DEFAULT_INT_VALUE) === $el->parent_id;
            });

            // Parent id not found, set root parent
            if ($parent === null) {
                $el->parent_id = MenuItem::DEFAULT_INT_VALUE;
                $el->save();

                return;
            }
            $parent['id'] = (int)($parent['id'] ?? MenuItem::DEFAULT_INT_VALUE);

            // Add to promises
            $promises[$parent['id']][] = $el;
        });

        if (!$items) {
            return $res;
        }

        $fakeToReal = [];

        foreach ($items as $index => &$el) {
            $fakeId = $el['id'];
            $el['priority'] = MenuItem::PRIORITY_DEFAULT + (int)$index;
            $el['active'] = !empty($el['active']);
            $el['marked'] = !empty($el['marked']);
            $parentId = (int)($el['parent_id'] ?? MenuItem::DEFAULT_INT_VALUE);
            $pageId = (int)($el['page_id'] ?? MenuItem::DEFAULT_INT_VALUE);
            $pageComponentId = (int)($el['page_component_id'] ?? MenuItem::DEFAULT_INT_VALUE);

            unset($el['id'], $el['parent_id'], $el['page_id'], $el['page_component_id']);
            /** @var MenuItem $newElement */
            $newElement = (new MenuItem($el))->menu()->associate($model);
            $newElement->parent_id = $parentId;

            if ($newElement->hasParent() && !in_array($newElement->parent_id, $curIds)) {
                $newElement->parent_id = Arr::get($fakeToReal, $newElement->parent_id, MenuItem::DEFAULT_INT_VALUE);
            }

            $newElement->page_id = $pageId;
            $newElement->page_component_id = $pageComponentId;
            $newElement->save();

            $fakeToReal[$fakeId] = $newElement->id;

            if (empty($promises[$fakeId])) {
                continue;
            }

            foreach ($promises[$fakeId] as $child) {
                $child->parent_id = $newElement->id;
                $child->save();
            }
            unset($promises[$fakeId]);
        }
        unset($el, $index, $items, $fakeToReal, $fakeId, $promises);

        return $res;
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
