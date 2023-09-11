<?php

declare(strict_types=1);

namespace Kelnik\Menu\Platform\Screens;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Menu\Models\Menu;
use Kelnik\Menu\Platform\Layouts\Menu\EditLayout;
use Kelnik\Menu\Platform\Listeners\TypeListener;
use Kelnik\Menu\Platform\Services\MenuPlatformService;
use Kelnik\Menu\Platform\Traits\MenuRepository;
use Kelnik\Menu\Providers\MenuServiceProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layout;

final class EditScreen extends Screen
{
    use MenuRepository;

    private bool $exists = false;
    private ?string $title = null;

    public ?Menu $menu = null;

    public function query(Menu $menu): array
    {
        $this->name = trans('kelnik-menu::admin.menu.title');
        $this->exists = $menu->exists;

        if ($this->exists) {
            $this->name = $menu->title;
        }

        return [
            'menu' => $menu,
            'types' => $this->getTypes()
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-menu::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('menu.list')),

            Button::make(trans('kelnik-menu::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeMenu')
                ->confirm(trans('kelnik-menu::admin.deleteConfirm', ['title' => $this->menu?->title]))
                ->canSee($this->exists),
        ];
    }

    /**
     * @return Layout[]|string[]
     * @throws BindingResolutionException
     */
    public function layout(): array
    {
        return [
            EditLayout::class,
            TypeListener::class,
            \Orchid\Support\Facades\Layout::rows([
                Button::make(trans('kelnik-menu::admin.save'))
                 ->icon('bs.save')
                 ->class('btn btn-secondary')
                 ->method('saveMenu')
            ]),
            \Orchid\Support\Facades\Layout::modal(
                'menuItem',
                \Orchid\Support\Facades\Layout::rows([
                    Input::make('item.id')->type('hidden'),
                    Input::make('item.title')->title('kelnik-menu::admin.title'),
                    Switcher::make('item.active')->title('kelnik-menu::admin.active'),
                    Switcher::make('item.marked')->title('kelnik-menu::admin.marked'),
                    Select::make('item.page_id')
                        ->title('kelnik-menu::admin.page')
                        ->options(
                            resolve(PageService::class)
                                ->getPagesWithoutDynamicComponents()
                                ->pluck('title', 'id')
                        )
                        ->empty(trans('kelnik-menu::admin.noValue'), MenuPlatformService::NO_VALUE),
                    Select::make('item.page_component_id')
                        ->title('kelnik-menu::admin.pageComponent')
                        ->options([])
                        ->empty(trans('kelnik-menu::admin.noValue'), MenuPlatformService::NO_VALUE),
                    Input::make('item.link')
                        ->title('kelnik-menu::admin.link')
                        ->help('kelnik-menu::admin.linkHelp'),
                    Picture::make('item.icon_image')
                        ->title('kelnik-menu::admin.iconImage')
                        ->groups(MenuServiceProvider::MODULE_NAME)
                        ->value('')
                        ->targetId(),
//                    Matrix::make('params')->keyValue(true)
//                        ->title('kelnik-menu::admin.params')
//                        ->columns([
//                            trans('kelnik-menu::admin.key') => 'key',
//                            trans('kelnik-menu::admin.value') => 'value'
//                        ])
                ])
            )->title(trans('kelnik-menu::admin.modalTitle'))->rawClick()
        ];
    }

    public function saveMenu(Request $request): RedirectResponse
    {
        return $this->menuPlatformService->save($this->menu, $request);
    }

    public function removeMenu(Menu $menu): RedirectResponse
    {
        return $this->menuPlatformService->remove($menu);
    }
}
