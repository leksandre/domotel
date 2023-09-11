<?php

declare(strict_types=1);

namespace Kelnik\Progress\Platform\Layouts\Group;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Progress\Models\Group;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

final class ListLayout extends Table
{
    protected $target = 'list';

    protected function columns(): array
    {
        /** @var CoreService $coreService */
        $coreService = $this->query->get('coreService');

        return [
            TD::make('id', trans('kelnik-progress::admin.id'))->defaultHidden(),
            TD::make('title', trans('kelnik-progress::admin.title'))
                ->render(
                    static fn(Group $group) => Link::make($group->title)
                        ->route($coreService->getFullRouteName('progress.group'), $group)
                ),

            TD::make()
                ->render(static function (Group $group) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';
                    $str .= '<div class="form-group mb-0">' .
                        \view('kelnik-core::platform.booleanState', ['state' => $group->active]) .
                        '</div>';
                    $str .= Link::make()->icon('pencil')
                        ->route($coreService->getFullRouteName('progress.group'), $group);
                    $str .= Button::make()->icon('bs.trash3')
                        ->action(route(
                            $coreService->getFullRouteName('progress.group'),
                            [$group, 'method' => 'removeAlbum']
                        ))
                        ->confirm(trans('kelnik-progress::admin.deleteConfirm', ['title' => $group->title]));
                    $str .= '</div>';

                    return $str;
                })->cantHide(),
        ];
    }
}
