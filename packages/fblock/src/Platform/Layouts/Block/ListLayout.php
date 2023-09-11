<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Platform\Layouts\Block;

use Kelnik\FBlock\Models\FlatBlock;
use Orchid\Icons\IconComponent;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

final class ListLayout extends Table
{
    protected $target = 'list';
    protected $template = 'kelnik-core::platform.layouts.tableSortable';

    protected function columns(): array
    {
        $coreService = $this->query->get('coreService');

        return [
//            TD::make('id', trans('kelnik-fblock::admin.id'))
//                ->sort()
//                ->filter(TD::FILTER_NUMERIC)
//                ->defaultHidden(),
            TD::make('title', trans('kelnik-fblock::admin.title'))
                ->render(function (FlatBlock $el) use ($coreService) {
                    return resolve(IconComponent::class, [
                            'path' => 'kelnik.sort',
                            'width' => '1.5em',
                            'height' => '1.5em',
                            'class' => 'handle me-3 float-start'
                        ])->render()() .
                        ' ' .
                        Link::make('[' . $el->getKey() . '] ' . $el->title)
                            ->route($coreService->getFullRouteName('fblock.element'), $el);
                }),
            TD::make()
                ->render(static function (FlatBlock $el) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';
                    $str .= '<div class="form-group mb-0">' .
                                \view('kelnik-core::platform.booleanState', ['state' => $el->active]) .
                            '</div>';
                    $str .= Link::make()->icon('pencil')
                            ->route($coreService->getFullRouteName('fblock.element'), $el);
                    $str .= Button::make()->icon('bs.trash3')
                                ->action(route(
                                    $coreService->getFullRouteName('fblock.element'),
                                    [$el, 'method' => 'removeElement']
                                ))
                                ->confirm(trans('kelnik-fblock::admin.deleteConfirm', ['title' => $el->title]));
                    $str .= '</div>';

                    return $str;
                })
                ->cantHide(false),
            TD::make('created_at', trans('kelnik-fblock::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-fblock::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
        ];
    }
}
