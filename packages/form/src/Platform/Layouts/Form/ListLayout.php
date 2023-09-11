<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Layouts\Form;

use Kelnik\Form\Models\Form;
use Orchid\Screen\Actions\Button;
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
                ->filter(TD::FILTER_NUMERIC)
                ->defaultHidden(),
            TD::make('title', trans('kelnik-form::admin.title'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(
                    static fn(Form $form) => Link::make($form->title)
                        ->route($coreService->getFullRouteName('form.edit'), $form)
                ),
            TD::make('fields_count', trans('kelnik-form::admin.fields.count'))
                ->render(static fn(Form $form) => $form->fields_count),
            TD::make('emails_count', trans('kelnik-form::admin.emails.count'))
                ->render(static fn(Form $form) => $form->emails_count),
            TD::make('logs_count', trans('kelnik-form::admin.logs.count'))
                ->render(static fn(Form $form) => $form->logs_count),
            TD::make()
                ->render(static function (Form $form) use ($coreService) {
                    $str = '<div class="admin-page-list_menu">';
                    $str .= '<div class="form-group mb-0">' .
                                \view('kelnik-core::platform.booleanState', ['state' => $form->active]) .
                            '</div>';
                    $str .= Link::make()->icon('bs.pencil')
                            ->route($coreService->getFullRouteName('form.edit'), $form);
                    $str .= Link::make()->icon('bs.gear')
                        ->route($coreService->getFullRouteName('form.field.list'), $form);
                    $str .= Link::make()->icon('bs.database')
                        ->route($coreService->getFullRouteName('form.log.list'), $form);
                    $str .= Button::make()->icon('bs.trash3')
                                ->action(route(
                                    $coreService->getFullRouteName('form.edit'),
                                    [
                                        $form,
                                        'method' => 'removeForm'
                                    ]
                                ))
                                ->confirm(trans('kelnik-form::admin.deleteConfirm', ['title' => $form->title]));
                    $str .= '</div>';

                    return $str;
                })->cantHide(false),
            TD::make('created_at', trans('kelnik-form::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-form::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
        ];
    }
}
