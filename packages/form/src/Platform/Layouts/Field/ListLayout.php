<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Layouts\Field;

use Kelnik\Form\Fields\Contracts\FieldType;
use Kelnik\Form\Models\Field;
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
        $form = $this->query->get('form');
        $coreService = $this->query->get('coreService');

        return [
            TD::make('title', trans('kelnik-form::admin.title'))
                ->render(
                    fn(Field $field) => resolve(IconComponent::class, [
                            'path' => 'kelnik.sort',
                            'width' => '1.5em',
                            'height' => '1.5em',
                            'class' => 'handle me-3 float-start'
                        ])->render()() .
                        ' ' .
                        Link::make($field->title)->route(
                            $coreService->getFullRouteName('form.field.edit'),
                            ['form' => $form, 'field' => $field]
                        )
                ),
            TD::make('type', trans('kelnik-form::admin.fieldType'))
                ->render(
                    static fn(Field $field) => is_a($field->type, FieldType::class, true)
                        ? $field->type::getTypeTitle()
                        : '-'
                ),
            TD::make('created_at', trans('kelnik-form::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-form::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make()
                ->render(static function (Field $field) use ($form, $coreService) {
                    $str = '<div class="admin-page-list_menu">';
                    $str .= '<div class="form-group mb-0">' .
                                \view('kelnik-core::platform.booleanState', ['state' => $field->active]) .
                            '</div>';
                    $str .= Link::make()->icon('bs.pencil')
                            ->route(
                                $coreService->getFullRouteName('form.field.edit'),
                                ['form' => $form, 'field' => $field]
                            );
                    $str .= Button::make()->icon('bs.trash3')
                                ->action(route(
                                    $coreService->getFullRouteName('form.field.edit'),
                                    ['form' => $form, 'field' => $field, 'method' => 'removeField']
                                ))
                                ->confirm(trans('kelnik-form::admin.deleteConfirm', ['title' => $field->title]));
                    $str .= '</div>';

                    return $str;
                })->cantHide()
        ];
    }
}
