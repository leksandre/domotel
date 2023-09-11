<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Form\Fields\Contracts\FieldDataProvider;
use Kelnik\Form\Fields\Contracts\FieldType;
use Kelnik\Form\Models\Form;
use Kelnik\Form\Repositories\Contracts\FormFieldRepository;
use Kelnik\Form\Repositories\Contracts\FormRepository;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Field;
use Orchid\Support\Facades\Toast;

final class FormPlatformService implements Contracts\FormPlatformService
{
    private array $fieldTypes = [];

    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly FormFieldRepository $formFieldRepository,
        private readonly CoreService $coreService
    ) {
        foreach (config('kelnik-form.fieldTypes', []) as $fieldClass) {
            $this->fieldTypes[$fieldClass] = $fieldClass::getTypeTitle();
        }
    }

    public function addFieldTypes(array $fields): void
    {
        $this->fieldTypes = array_merge($this->fieldTypes, $fields);
    }

    public function getFieldTypes(): array
    {
        return $this->fieldTypes;
    }

    public function getContentLink(): Field
    {
        return Link::make(trans('kelnik-form::admin.formLink'))
                ->route($this->coreService->getFullRouteName('form.list'))
                ->icon('bs.info')
                ->class('btn btn-info')
                ->target('_blank')
                ->style('display: inline-block; margin-bottom: 20px');
    }

    public function getList(): Collection
    {
        return $this->formRepository->getAll()->pluck('title', 'id');
    }

    public function saveFormFromPlatform(Form $form, Request $request): RedirectResponse
    {
        $formData = $request->only([
            'form.policy_page_id',
            'form.active',
            'form.title',
            'form.success_title',
            'form.error_title',
            'form.notify_title',
            'form.slug',
            'form.description',
            'form.button_text',
            'form.success_text',
            'form.error_text',
        ]);
        $emails = $request->input('form.emails') ?? [];

        $rules = [
            'form.policy_page_id' => 'nullable|numeric',
            'form.active' => 'boolean',
            'form.title' => 'required|max:255',
            'form.success_title' => 'nullable|max:255',
            'form.error_title' => 'nullable|max:255',
            'form.notify_title' => 'nullable|max:255',
            'form.slug' => 'required|max:255|regex:/^[a-z0-9\-_]+$/i'
        ];

        if ($emails) {
            $rules['form.emails.*.email'] = 'required|email:filter';
        }

        $request->validate($rules);

        $formData = Arr::get($formData, 'form');
        $form->fill($formData);

        $emails = array_column($emails, 'email');

        $this->formRepository->save($form, $emails ?: []);
        Toast::info(trans('kelnik-form::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('form.list'));
    }

    public function saveFormFieldFromPlatform(
        \Kelnik\Form\Models\Field $field,
        Request $request,
        FieldDataProvider $dataProvider
    ): RedirectResponse {
        $dataProvider->validateRequest($field, $request);
        $dataProvider->setDataFromRequest($field, $request);

        $this->formFieldRepository->save($field);
        Toast::info(trans('kelnik-form::admin.saved'));

        return redirect()->route(
            $this->coreService->getFullRouteName('form.field.list'),
            $field->form
        );
    }

    public function addField(Form $form, string $fieldType): bool
    {
        $fieldTypeExists = in_array($fieldType, array_keys($this->getFieldTypes()));

        if (!$form->exists || !$fieldTypeExists) {
            throw new InvalidArgumentException(trans('kelnik-form::admin.errors.fieldTypeNotFound'));
        }

        if (!is_a($fieldType, FieldType::class, true)) {
            throw new InvalidArgumentException(trans('kelnik-form::admin.errors.isNotFieldType'));
        }

        $field = new \Kelnik\Form\Models\Field([
            'type' => $fieldType,
            'priority' => ($form->fields()->max('priority') ?? \Kelnik\Form\Models\Field::PRIORITY_DEFAULT) + 1,
            'title' => $fieldType::getTypeTitle()
        ]);

        $field->form()->associate($form);

        return $this->formFieldRepository->save($field);
    }

    public function sortFields(array $fieldsPriority): bool
    {
        /** @var FormFieldRepository $fieldsRepo */
        $fieldsRepo = resolve(FormFieldRepository::class);
        $fields = $fieldsRepo->getAll();

        if ($fields->isEmpty()) {
            return false;
        }

        $fields->each(static function (\Kelnik\Form\Models\Field $el) use ($fieldsPriority, $fieldsRepo) {
            $el->priority = (int)array_search($el->getKey(), $fieldsPriority) +
                \Kelnik\Form\Models\Field::PRIORITY_DEFAULT;
            $fieldsRepo->save($el);
        });

        return true;
    }

    public function createSlugByTitle(string $title): string
    {
        return Str::slug($title);
    }
}
