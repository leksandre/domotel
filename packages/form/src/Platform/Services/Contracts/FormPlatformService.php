<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Kelnik\Form\Fields\Contracts\FieldDataProvider;
use Kelnik\Form\Models\Form;
use Orchid\Screen\Field;

interface FormPlatformService
{
    /**
     * @param string[] $fields
     *
     * @return void
     */
    public function addFieldTypes(array $fields): void;

    public function getFieldTypes(): array;

    public function getContentLink(): Field;

    public function getList(): Collection;

    public function saveFormFromPlatform(Form $form, Request $request): RedirectResponse;

    public function saveFormFieldFromPlatform(
        \Kelnik\Form\Models\Field $field,
        Request $request,
        FieldDataProvider $dataProvider
    ): RedirectResponse;

    public function addField(Form $form, string $fieldType): bool;

    public function sortFields(array $fieldsPriority): bool;

    public function createSlugByTitle(string $title): string;
}
