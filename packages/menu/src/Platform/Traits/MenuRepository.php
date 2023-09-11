<?php

declare(strict_types=1);

namespace Kelnik\Menu\Platform\Traits;

use Illuminate\Support\Facades\Route;
use Kelnik\Menu\Models\Enums\Type;
use Kelnik\Menu\Models\Menu;

trait MenuRepository
{
    private function getMenu(): Menu
    {
        $request = request();
        $prevUrl = parse_url($request->header('referer'), PHP_URL_PATH);
        $menuId = Route::getRoutes()->match($request->create($prevUrl))?->parameter('menu', 0);

        return $menuId
            ? resolve(\Kelnik\Menu\Repositories\Contracts\MenuRepository::class)->findByPrimary($menuId)
            : new Menu();
    }

    private function getTypes(): array
    {
        $types = [];

        foreach (Type::cases() as $type) {
            $types[$type->value] = $type->title();
        }

        return $types;
    }
}
