<?php

declare(strict_types=1);

namespace Kelnik\Contact\Platform\Layouts\Office;

use Kelnik\Contact\Models\Office;
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
//            TD::make('id', trans('kelnik-contact::admin.id'))
//                ->sort()
//                ->filter(TD::FILTER_NUMERIC)
//                ->defaultHidden(),
            TD::make('title', trans('kelnik-contact::admin.title'))
                ->render(function (Office $office) use ($coreService) {
                    return resolve(IconComponent::class, [
                            'path' => 'kelnik.sort',
                            'width' => '1.5em',
                            'height' => '1.5em',
                            'class' => 'handle me-3 float-start'
                        ])->render()() .
                        ' ' .
                        Link::make($office->title)
                        ->route($coreService->getFullRouteName('contact.office.edit'), $office);
                }),
            TD::make('created_at', trans('kelnik-contact::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-contact::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make()
                ->render(static function (Office $office) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';
                    $str .= '<div class="form-group mb-0">' .
                                \view('kelnik-core::platform.booleanState', ['state' => $office->active]) .
                            '</div>';
                    $str .= Link::make()->icon('pencil')
                        ->route($coreService->getFullRouteName('contact.office.edit'), $office);
                    $str .= Button::make()->icon('bs.trash3')
                        ->action(route(
                            $coreService->getFullRouteName('contact.office.edit'),
                            ['office' => $office->getKey(), 'method' => 'removeOffice']
                        ))
                        ->confirm(trans('kelnik-contact::admin.deleteConfirm', ['title' => $office->title]));
                    $str .= '</div>';

                    return $str;
                })->cantHide()
        ];
    }
}
