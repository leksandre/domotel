<?php

declare(strict_types=1);

namespace Kelnik\Menu\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Menu\Models\Menu;
use Kelnik\Menu\Models\MenuItem;
use Kelnik\Menu\Repositories\Contracts\MenuRepository;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Field;

final class MenuService implements Contracts\MenuService
{
    public function __construct(
        private readonly MenuRepository $menuRepository,
        private readonly CoreService $coreService
    ) {
    }

    public function getContentLink(): Field
    {
        return Link::make(trans('kelnik-menu::admin.menuLink'))
                ->route($this->coreService->getFullRouteName('menu.list'))
                ->icon('bs.info')
                ->class('btn btn-info')
                ->target('_blank')
                ->style('display: inline-block; margin-bottom: 20px');
    }

    public function getList(): Collection
    {
        return $this->menuRepository->getAll()->pluck('title', 'id');
    }

    public function buildMenu(int|string $primaryKey, Request $request): Menu
    {
        $menu = $this->menuRepository->findActiveByPrimary($primaryKey);

        if (!$menu->exists) {
            return $menu;
        }

        $itemLink = [];
        $attachments = [];
        $menu->setRelation('items', new Collection());
        $this->menuRepository->getElements($menu)
            ->map(static function (MenuItem $item) use (&$menu, &$itemLink, &$attachments) {
                if (
                    ($item->page_id && !$item->page->active)
                    || ($item->page_component_id && !$item->pageComponent->active)
                ) {
                    return true;
                }

                if (
                    $item->relationLoaded('icon')
                    && $item->icon->exists
                    && strtolower($item->icon->mime) === 'image/svg+xml'
                ) {
                    if (!isset($attachments[$item->icon->hash])) {
                        $attachments[$item->icon->hash] = Storage::disk($item->icon->disk)->get(
                            $item->icon->physicalPath()
                        );
                    }

                    $item->iconBody = $attachments[$item->icon->hash];
                }

                $itemLink[$item->id] = &$item;

                if ($item->hasParent()) {
                    if (!isset($itemLink[$item->parent_id])) {
                        return true;
                    }

                    if (!$itemLink[$item->parent_id]->relationLoaded('children')) {
                        $itemLink[$item->parent_id]->setRelation('children', new Collection());
                    }
                    $itemLink[$item->parent_id]->children->add($item);

                    return true;
                }

                $menu->items->add($item);
            });
        unset($itemLink, $attachments);

        return $menu;
    }

    public function setSelectedItems(Menu $menu, Request $request): Menu
    {
        if (!$menu->relationLoaded('items') || !$menu->items->isNotEmpty()) {
            return $menu;
        }

        $menu->items->each(fn ($item) => $this->checkMenuItemForSelection($item, $request));

        return $menu;
    }

    private function checkMenuItemForSelection(MenuItem &$menuItem, Request $request): void
    {
        if ($menuItem->relationLoaded('children') && $menuItem->children->isNotEmpty()) {
            $menuItem->children->each(fn ($item) => $this->checkMenuItemForSelection($item, $request));
        }

        if (!$menuItem->url) {
            return;
        }

        $path = parse_url($menuItem->url, PHP_URL_PATH);
        $curPath = parse_url($request->getPathInfo(), PHP_URL_PATH);

        $menuItem->selected = $path === $curPath && $path !== null;
    }

    public function getCacheTag(int|string $id): ?string
    {
        return 'menu_' . $id;
    }
}
