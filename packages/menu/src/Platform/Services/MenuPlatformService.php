<?php

declare(strict_types=1);

namespace Kelnik\Menu\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Menu\Models\Menu;
use Kelnik\Menu\Repositories\Contracts\MenuRepository;
use Orchid\Support\Facades\Toast;

final class MenuPlatformService implements Contracts\MenuPlatformService
{
    public const NO_VALUE = '0';

    public function __construct(
        private readonly MenuRepository $menuRepository,
        private readonly CoreService $coreService
    ) {
    }

    public function save(Menu $menu, Request $request): RedirectResponse
    {
        $menuData = $request->only([
            'menu.title',
            'menu.type',
            'menu.active',
        ]);

        $menuData = Arr::get($menuData, 'menu');
        $menu->fill($menuData);

        $items = $request->input('items') ?? [];
        $items = array_map(static fn(array $el) => Arr::except($el, ['icon_path', 'url']), $items);

        $this->menuRepository->save($menu, $items);
        Toast::info(trans('kelnik-menu::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('menu.list'));
    }

    public function remove(Menu $menu): RedirectResponse
    {
        $this->menuRepository->delete($menu)
            ? Toast::info(trans('kelnik-menu::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('menu.list'));
    }
}
