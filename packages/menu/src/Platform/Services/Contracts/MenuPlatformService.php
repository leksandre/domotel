<?php

declare(strict_types=1);

namespace Kelnik\Menu\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Menu\Models\Menu;
use Kelnik\Menu\Repositories\Contracts\MenuRepository;

interface MenuPlatformService
{
    public function __construct(MenuRepository $menuRepository, CoreService $coreService);

    public function save(Menu $menu, Request $request): RedirectResponse;

    public function remove(Menu $menu): RedirectResponse;
}
