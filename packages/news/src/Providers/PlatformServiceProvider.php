<?php

declare(strict_types=1);

namespace Kelnik\News\Providers;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\News\Models\Category;
use Kelnik\News\Platform\Services\Contracts\NewsPlatformService;
use Kelnik\News\Repositories\Contracts\CategoryRepository;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

final class PlatformServiceProvider extends OrchidServiceProvider
{
    public array $bindings = [
        NewsPlatformService::class => \Kelnik\News\Platform\Services\NewsPlatformService::class
    ];

    public function registerMainMenu(): array
    {
        $menuElements = [];
        $startPosition = 100;
        $globalActive = [];
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        resolve(CategoryRepository::class)
            ->getAll()
            ->each(
                static function (Category $category) use (
                    &$menuElements,
                    &$startPosition,
                    &$title,
                    &$globalActive,
                    $coreService
                ) {
                    $active = [
                        route(
                            $coreService->getFullRouteName('news.category.elements'),
                            ['category' => $category]
                        ) . '*',
                        route(
                            $coreService->getFullRouteName('news.category.element'),
                            ['category' => $category]
                        ) . '/*'
                    ];
                    $globalActive = array_merge($globalActive, $active);
                    $menuElements[] = Menu::make($category->title)
                        ->title($title)
                        ->sort($startPosition++)
                        ->icon('bs.file-earmark')
                        ->route(
                            $coreService->getFullRouteName('news.category.elements'),
                            ['category' => $category]
                        )
                        ->active($active)
                        ->permission(NewsServiceProvider::MODULE_PERMISSION);

                    $title = null;
                }
            );

        $active = [
            $coreService->getFullRouteName('news.elements'),
            $coreService->getFullRouteName('news.element')
        ];
        $globalActive = array_merge($globalActive, $active);

        $menuElements[] = Menu::make('kelnik-news::admin.menu.elements')
            ->title($title)
            ->sort($startPosition++)
            ->icon('bs.file-earmark')
            ->route($coreService->getFullRouteName('news.elements'))
            ->active($active)
            ->permission(NewsServiceProvider::MODULE_PERMISSION);

        $active = [
            $coreService->getFullRouteName('news.categories'),
            $coreService->getFullRouteName('news.category')
        ];
        $globalActive = array_merge($globalActive, $active);

        $menuElements[] = Menu::make('kelnik-news::admin.menu.categories')
            ->sort(190)
            ->icon('bs.gear')
            ->route($coreService->getFullRouteName('news.categories'))
            ->active($active)
            ->permission(NewsServiceProvider::MODULE_PERMISSION);

        return [
            Menu::make(trans('kelnik-news::admin.menu.title'))
                ->title(trans('kelnik-core::admin.menuDatabase'))
                ->icon('bs.database')
                ->sort(1100)
                ->list($menuElements)
                ->active($globalActive)
                ->permission(NewsServiceProvider::MODULE_PERMISSION)
                ->addBeforeRender(function () {
                    $list = $this->get('list');

                    if (!$list) {
                        return null;
                    }

                    $this->set(
                        'list',
                        $list->sort(fn($current, $next) => $current->get('sort') <=> $next->get('sort'))
                    );
                })
        ];
    }

    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(trans('kelnik-news::admin.menu.title'))
                ->addPermission(NewsServiceProvider::MODULE_PERMISSION, trans('kelnik-news::admin.permission'))
        ];
    }
}
