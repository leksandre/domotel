<?php

declare(strict_types=1);

namespace Kelnik\Menu\Services\Contracts;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Kelnik\Menu\Models\Menu;
use Orchid\Screen\Field;

interface MenuService
{
    public function getContentLink(): Field;

    public function getList(): Collection;

    public function buildMenu(int|string $primaryKey, Request $request): Menu;

    public function setSelectedItems(Menu $menu, Request $request): Menu;

    public function getCacheTag(int|string $id): ?string;
}
