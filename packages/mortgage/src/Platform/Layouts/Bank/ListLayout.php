<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Platform\Layouts\Bank;

use Kelnik\Mortgage\Models\Bank;
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
//            TD::make('id', trans('kelnik-mortgage::admin.id'))->defaultHidden(),
            TD::make('title', trans('kelnik-mortgage::admin.title'))
                ->render(
                    static fn(Bank $bank) => resolve(IconComponent::class, [
                            'path' => 'kelnik.sort',
                            'width' => '1.5em',
                            'height' => '1.5em',
                            'class' => 'handle me-3 float-start'
                        ])->render()() .
                        ' ' .
                        Link::make($bank->title)->route($coreService->getFullRouteName('mortgage.bank'), $bank)
                ),
            TD::make('programs_count', trans('kelnik-mortgage::admin.programsCount'))
                ->render(fn (Bank $bank) => Link::make((string)$bank->programs_count)),
            TD::make('created_at', trans('kelnik-mortgage::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-mortgage::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),

            TD::make()
                ->render(static function (Bank $bank) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';
                    $str .= '<div class="form-group mb-0">' .
                        \view('kelnik-core::platform.booleanState', ['state' => $bank->active]) .
                        '</div>';
                    $str .= Link::make()->icon('pencil')
                        ->route($coreService->getFullRouteName('mortgage.bank'), $bank);
                    $str .= Button::make()->icon('bs.trash3')
                        ->action(route(
                            $coreService->getFullRouteName('mortgage.bank'),
                            [$bank, 'method' => 'removeBank']
                        ))
                        ->confirm(trans('kelnik-mortgage::admin.deleteConfirm', ['title' => $bank->title]));
                    $str .= '</div>';

                    return $str;
                })->cantHide(),
        ];
    }
}
