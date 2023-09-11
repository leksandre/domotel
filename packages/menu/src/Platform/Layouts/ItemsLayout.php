<?php

declare(strict_types=1);

namespace Kelnik\Menu\Platform\Layouts;

use Illuminate\Support\Collection;
use Kelnik\Menu\Models\Menu;
use Kelnik\Menu\Models\MenuItem;
use Orchid\Screen\Layout;
use Orchid\Screen\Repository;

final class ItemsLayout extends Layout
{
    public function build(Repository $repository)
    {
        $data = [
            'modalId' => 'menuItem',
            'translates' => base64_encode(json_encode([
                'newElement' => trans('kelnik-menu::admin.tree.newElement'),
                'errorHeader' => trans('kelnik-menu::admin.tree.errorHeader'),
                'deleteConfirm' => trans('kelnik-menu::admin.tree.deleteConfirm'),
                'addButton' => trans('kelnik-menu::admin.tree.addButton'),
                'blocks' => trans('kelnik-menu::admin.strict.blocks'),
                'block' => trans('kelnik-menu::admin.strict.block'),
                'addBlock' => trans('kelnik-menu::admin.strict.addBlock'),
                'row' => trans('kelnik-menu::admin.strict.row'),
                'addRowElement' => trans('kelnik-menu::admin.strict.addRowElement'),
            ])),
            'content' => []
        ];

        /** @var Menu $menu */
        $menu = $repository->get('menu');
        $menuItems = $menu->items()->with(['icon', 'page', 'pageComponent'])->get();

        $menuItems->each(function (MenuItem $item, $key) use (&$data, &$menuItems) {
            if (!$item->hasParent()) {
                $menuItems->forget($key);
                $data['content'][] = $this->buildTree($item, $menuItems);
            }
        });
        unset($menuItems);

        $data['content'] = base64_encode(json_encode($data['content']));

        return view(
            $menu->type->isStrict()
            ? 'kelnik-menu::platform.layouts.menuStrict'
            : 'kelnik-menu::platform.layouts.menuTree',
            $data
        );
    }

    private function buildTree(MenuItem $item, Collection $menuItems): array
    {
        $res = [
            'id' => $item->getKey(),
            'pid' => $item->parent_id,
            'parent_id' => $item->parent_id,
            'page_id' => $item->page_id,
            'page_component_id' => $item->page_component_id,
            'icon_image' => $item->icon_image,
            'icon_path' => $item->icon->url(),
            'active' => $item->active,
            'marked' => $item->marked,
            'name' => $item->title,
            'link' => $item->link,
            'url' => $item->url,
            'params' => $item->params,
            'isLeaf' => false,
            'addLeafNodeDisabled' => true,
            'editNodeDisabled' => true,
            'children' => []
        ];

        if ($menuItems->isEmpty()) {
            return $res;
        }

        $menuItems->each(function (MenuItem $otherItem, $key) use ($item, &$res, &$menuItems) {
            if ($otherItem->parent_id === $item->id) {
                $menuItems->forget($key);
                $res['children'][] = $this->buildTree($otherItem, $menuItems);
            }
        });

        return $res;
    }
}
