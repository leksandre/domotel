<?php

declare(strict_types=1);

namespace Kelnik\Menu\Platform\Listeners;

use Illuminate\Http\Request;
use Kelnik\Menu\Models\Enums\Type;
use Kelnik\Menu\Platform\Layouts\ItemsLayout;
use Kelnik\Menu\Platform\Traits\MenuRepository;
use Orchid\Screen\Layout;
use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Repository;

final class TypeListener extends Listener
{
    use MenuRepository;

    /** @var string[] */
    protected $targets = ['menu.type'];

    /** @return Layout[] */
    protected function layouts(): iterable
    {
        return [
            ItemsLayout::class
        ];
    }

    public function handle(Repository $repository, Request $request): Repository
    {
        $type = (int)($request->input('menu.type') ?? Type::Tree->value);
        $menu = $this->getMenu();
        $menu->type = Type::tryFrom($type);

        $repository->set('menu', $menu);
        $repository->set('types', $this->getTypes());

        return $repository;
    }
}
