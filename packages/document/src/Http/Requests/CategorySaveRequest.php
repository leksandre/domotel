<?php

declare(strict_types=1);

namespace Kelnik\Document\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Date;
use Kelnik\Document\Dto\CategoryDto;
use Kelnik\Document\Models\Group;

final class CategorySaveRequest extends FormRequest
{
    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'category.title' => 'required|max:255',
            'category.slug' => 'nullable|max:255|regex:/^[a-z0-9\-_.]+$/i',
            'category.active' => 'boolean',
            'category.group_id' => 'nullable|integer',
            'elements' => 'nullable|array',
            'elements.*.active' => 'boolean',
            'elements.*.title' => 'required|max:255',
            'elements.*.author' => 'max:255',
            'elements.*.publish_date' => 'nullable|date_format:Y-m-d H:i:s',
            'elements.*.attachment' => 'nullable|integer',
        ];
    }

    public function getDto(): CategoryDto
    {
        $elements = array_values($this->input('elements'));

        foreach ($elements as &$el) {
            $el['id'] = (int)$el['id'] ?: null;
            $el['active'] = (bool)$el['active'];
            $el['publish_date'] = Date::make($el['publish_date']);
        }
        unset($el);

        $groupId = $this->integer('category.group_id') ?: null;
        $group = null;

        if ($groupId) {
            $group = new Group();
            $group->id = $groupId;
        }

        return new CategoryDto(
            $this->string('category.title')->toString(),
            $this->boolean('category.active'),
            $group,
            $this->user(),
            $this->string('category.slug')->toString() ?: null,
            $elements
        );
    }
}
