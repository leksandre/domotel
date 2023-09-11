<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Layouts\PageComponents;

use Illuminate\Support\Arr;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponentSection;
use Orchid\Icons\IconComponent;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

abstract class BaseListLayout extends Table
{
    protected $target = 'components.' . KelnikPageComponentSection::PAGE_COMPONENT_SECTION_CONTENT;
    protected $template = 'kelnik-core::platform.layouts.tableSortable';

    /** @return TD[] */
    protected function columns(): array
    {
        /** @var CoreService $coreService */
        $isAlone = (int)Arr::get($this->query, $this->target)?->count() === 1;
        $coreService = $this->query->get('coreService');

        return [
            TD::make('title', trans('kelnik-page::admin.title'))
                ->render(static function (PageComponent $pageComponent) use ($isAlone) {
                    $res = $pageComponent->data->getComponentTitle();

                    if ($isAlone) {
                        return $res;
                    }

                    return resolve(IconComponent::class, [
                            'path' => 'kelnik.sort',
                            'width' => '1.5em',
                            'height' => '1.5em',
                            'class' => 'handle me-3'
                        ])->render()() . ' ' . $res;
                }),
            TD::make('component', trans('kelnik-page::admin.component'))
                ->render(
                    static fn(PageComponent $pageComponent) => '<span style="color: rgba(33,37,41,.35)">' .
                        $pageComponent->data->getComponentTitleOriginal() .
                        '</span>'
                ),
            TD::make()
                ->render(function (PageComponent $pageComponent) use ($coreService) {
                    $page = $this->query->get('page');
                    $dataProvider = $pageComponent->data;

                    $str = '<div class="admin-page-list_menu">';

                    if ($pageComponent->available()) {
                        $str .= '<div class="form-group mb-0">' .
                            \view('kelnik-core::platform.booleanState', ['state' => $pageComponent->active]) .
                            '</div>';
                    }

                    $str .= Link::make()
                        ->icon('bs.gear')
                        ->route(
                            $coreService->getFullRouteName('page.component'),
                            [
                                'site' => $page->site_id,
                                'page' => $page->id,
                                'component' => $pageComponent->id
                            ]
                        )
                        ->canSee($pageComponent->editable());

                    $str .= Button::make()
                        ->icon('bs.trash3')
//                        ->method('removeComponent' . $pageComponent->id)
                        ->action(route(
                            $coreService->getFullRouteName('page.component'),
                            [
                                'site' => $page->site_id,
                                'page' => $page->id,
                                'component' => $pageComponent->id,
                                'method' => 'removePageComponent'
                            ]
                        ))
                        ->confirm(trans(
                            'kelnik-page::admin.deleteConfirm',
                            ['title' => $dataProvider->getComponentTitle()]
                        ))
                        ->canSee($pageComponent->removable());

                    $str .= '</div>';

                    return $str;
                }),
        ];
    }
}
