<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Header\Layouts;

use Closure;
use Illuminate\Support\Collection;
use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Range;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Form\Platform\Services\Contracts\FormPlatformService;
use Kelnik\Menu\Services\Contracts\MenuService;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\View\Components\Header\DataProvider;
use Kelnik\Page\View\Components\Header\Enums\TemplateType;
use Kelnik\Page\View\Components\Header\Header;
use Kelnik\Page\View\Components\Header\HeaderMenuTemplate;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

final class ContentLayout extends Rows
{
    public function __construct(
        private Fieldable|Groupable|Closure $tabFooter,
        private ?Collection $menuTemplates = null
    ) {
    }

    protected function fields(): array
    {
        $coreService = $this->query->get('coreService');

        $res = [
            Title::make('')->value(trans('kelnik-page::admin.components.header.logo.title')),
            Picture::make('data.content.logoLight')
                ->title('kelnik-page::admin.components.header.logo.light')
                ->targetId()
                ->groups(PageServiceProvider::MODULE_NAME),
            Picture::make('data.content.logoDark')
                ->title('kelnik-page::admin.components.header.logo.dark')
                ->targetId()
                ->groups(PageServiceProvider::MODULE_NAME)
                ->help('kelnik-page::admin.components.header.logo.help'),
            Range::make('data.content.logoHeight')
                ->title('kelnik-page::admin.components.header.logo.height')
                ->min(Header::LOGO_HEIGHT_MIN)
                ->max(Header::LOGO_HEIGHT_MAX)
                ->value(Header::LOGO_HEIGHT_MIN)
                ->hr()
        ];

        if ($coreService->hasModule('form')) {
            /** @var FormPlatformService $formPlatformService */
            $formPlatformService = resolve(FormPlatformService::class);
            $res[] = Title::make('')->value(trans('kelnik-page::admin.components.header.callback.title'));
            $res[] = Input::make('data.content.callbackButton.text')
                ->title('kelnik-page::admin.components.header.callback.text')
                ->help(
                    trans(
                        'kelnik-page::admin.components.header.callback.limit',
                        ['limit' => DataProvider::BUTTON_TEXT_LIMIT]
                    )
                )
                ->maxlength(DataProvider::BUTTON_TEXT_LIMIT);

            $res[] = Select::make('data.content.callbackButton.form_id')
                ->title('kelnik-page::admin.components.header.callback.form')
                ->options($formPlatformService->getList())
                ->empty(trans('kelnik-page::admin.components.header.noValue'), DataProvider::NO_VALUE);

            $res[] = $formPlatformService->getContentLink();
        }

        $res[] = Input::make('data.content.phone')
            ->title('kelnik-page::admin.components.header.phone.title')
            ->mask(['regex' => '[0-9()\-+ ]+'])
            ->maxlength(50)
            ->help('kelnik-page::admin.components.header.phone.help')
            ->hr();

        if ($coreService->hasModule('menu')) {
            /** @var MenuService $menuService */
            $menuService = resolve(MenuService::class);
            $menus = $menuService->getList();
            $menuTemplates = [];

            /** @var HeaderMenuTemplate $el */
            foreach ($this->menuTemplates as $el) {
                $menuTemplates[$el->type->value][$el->name] = $el->title;
            }

            $templateSelector = Select::make('data.content.menu.desktop.template')
                ->title('kelnik-page::admin.components.header.menu.template')
                ->empty(trans('kelnik-page::admin.components.header.noValue'), DataProvider::NO_VALUE);

            $res[] = Title::make('')->value(trans('kelnik-page::admin.components.header.menu.title'));
            $res[] = Select::make('data.content.menu.desktop.id')
                        ->title('kelnik-page::admin.components.header.menu.desktop')
                        ->options($menus)
                        ->empty(trans('kelnik-page::admin.components.header.noValue'), DataProvider::NO_VALUE);
            $res[] = (clone $templateSelector)->options(
                array_merge(
                    $menuTemplates[TemplateType::Desktop->value] ?? [],
                    $menuTemplates[TemplateType::Universal->value] ?? []
                )
            );

            $res[] = Select::make('data.content.menu.mobile.id')
                ->title('kelnik-page::admin.components.header.menu.mobile')
                ->options($menus)
                ->empty(trans('kelnik-page::admin.components.header.noValue'), DataProvider::NO_VALUE);
            $res[] = (clone $templateSelector)
                ->set('name', 'data.content.menu.mobile.template')
                ->options($menuTemplates[TemplateType::Mobile->value] ?? []);

            $res[] = $menuService->getContentLink();
            unset($menuService);
        }

        $res[] = is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter;

        return $res;
    }
}
