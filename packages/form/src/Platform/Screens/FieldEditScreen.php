<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Screens;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Form\Fields\Contracts\FieldDataProvider;
use Kelnik\Form\Fields\Contracts\FieldType;
use Kelnik\Form\Models\Field;
use Kelnik\Form\Models\Form;
use Kelnik\Form\Repositories\Contracts\FormFieldRepository;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Toast;
use Symfony\Component\HttpFoundation\Response;

final class FieldEditScreen extends Screen
{
    private bool $exists = false;
    private ?string $title = null;
    private ?Form $form = null;
    private ?Field $field = null;
    private ?FieldDataProvider $dataProvider = null;

    public function query(Form $form, Field $field): array
    {
        abort_if(!$field->exists || !$form->exists || $field->form()->isNot($form), Response::HTTP_NOT_FOUND);

        $this->form = $form;
        $this->field = $field;
        $this->name = trans('kelnik-form::admin.menu.title');
        $this->exists = $field->exists;

        if ($this->exists && is_a($this->field->type, FieldType::class, true)) {
           $this->name = $field->title;
           $this->description = trans(
               'kelnik-form::admin.fieldTypeTitle',
               ['title' => $this->field->type::getTypeTitle()]
           );
           $this->dataProvider = $this->field->type::initDataProvider();
        }

        return [
            'form' => $form,
            'field' => $field
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-form::admin.backToFields'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('form.field.list'), $this->form),

            Button::make(trans('kelnik-form::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeField')
                ->confirm(trans('kelnik-form::admin.deleteConfirm', ['title' => $this->title]))
                ->canSee($this->exists)
        ];
    }

    public function layout(): array
    {
        return $this->dataProvider
            ? $this->dataProvider->getEditLayouts()
            : [];
    }

    public function saveField(Form $form, Field $field, Request $request): RedirectResponse
    {
        return $this->formPlatformService->saveFormFieldFromPlatform(
            $field,
            $request,
            $field->type::initDataProvider()
        );
    }

    public function removeField(Form $form, Field $field): RedirectResponse
    {
        resolve(FormFieldRepository::class)->delete($field)
            ? Toast::info(trans('kelnik-form::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('form.field.list'), ['form' => $form]);
    }
}
