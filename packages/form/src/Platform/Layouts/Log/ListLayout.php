<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Layouts\Log;

use Kelnik\Form\Models\Log;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

final class ListLayout extends Table
{
    protected $target = 'list';

    protected function columns(): array
    {
        $coreService = $this->query->get('coreService');

        return [
            TD::make('id', trans('kelnik-form::admin.id'))
                ->sort()
                ->filter(TD::FILTER_NUMERIC),
            TD::make('title', trans('kelnik-form::admin.title'))
                ->render(static fn(Log $log) => $log->form->title),

            TD::make('created_at', trans('kelnik-form::admin.created'))
                ->sort()
                ->dateTimeString(),
            TD::make()
                ->render(static function (Log $log) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';
                    $str .= Link::make()->icon('bs.eye')
                            ->route(
                                $coreService->getFullRouteName('form.log.view'),
                                ['form' => $log->form, 'log' => $log]
                            );
                    $str .= '</div>';

                    return $str;
                })->cantHide()
        ];
    }
}
