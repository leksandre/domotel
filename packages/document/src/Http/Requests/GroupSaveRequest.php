<?php

declare(strict_types=1);

namespace Kelnik\Document\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Kelnik\Document\Dto\GroupDto;

final class GroupSaveRequest extends FormRequest
{
    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'group.title' => 'required|max:255',
            'group.active' => 'boolean'
        ];
    }

    public function getDto(): GroupDto
    {
        return new GroupDto(
            $this->string('group.title')->toString(),
            $this->boolean('group.active'),
            $this->user()
        );
    }
}
