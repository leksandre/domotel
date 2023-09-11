<?php

declare(strict_types=1);

namespace Kelnik\Form\Fields;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kelnik\Form\Fields\Contracts\FieldDataProvider;
use Kelnik\Form\Models\Field;

abstract class PlatformDataProvider implements FieldDataProvider
{
    public function __construct(private readonly string $fieldTypeNamespace)
    {
    }

    public function validateRequest(Field $field, Request $request): void
    {
        $request->validate([
            'field.active' => 'boolean',
            'field.title' => 'required|max:255',
            'field.params.attributes.required' => 'boolean',
            'field.params.attributes.name' => 'nullable|max:255|regex:/^[a-z0-9\-_]+$/i'
        ]);
    }

    public function setDataFromRequest(Field &$field, Request $request): void
    {
        $formData = $request->only([
            'field.active',
            'field.title',
            'field.params.attributes.name',
            'field.params.attributes.required',
            'field.params.attributes.placeholder',
//            'field.params.description'
        ]);

        $formData = Arr::get($formData, 'field');
        $formData['params']['attributes']['required'] = !empty($formData['params']['attributes']['required']);

        $field->fill($formData);
    }
}
