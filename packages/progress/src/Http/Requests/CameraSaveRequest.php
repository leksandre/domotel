<?php

declare(strict_types=1);

namespace Kelnik\Progress\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Kelnik\Progress\Dto\CameraDto;
use Kelnik\Progress\Models\Group;

final class CameraSaveRequest extends FormRequest
{
    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'camera.title' => 'required|max:255',
            'camera.url' => 'required|url|max:255',
            'camera.active' => 'boolean',
            'camera.description' => 'nullable|string',
            'camera.cover_image' => 'nullable|integer',
            'camera.group_id' => 'nullable|integer'
        ];
    }

    public function getDto(): CameraDto
    {
        $groupId = $this->integer('camera.group_id') ?: null;
        $group = null;

        if ($groupId) {
            $group = new Group();
            $group->id = $groupId;
        }

        return new CameraDto(
            $this->string('camera.title')->toString(),
            $this->string('camera.url')->toString(),
            $this->boolean('camera.active'),
            $this->string('camera.description')->toString(),
            $this->integer('camera.url') ?: null,
            $group,
            $this->user()
        );
    }
}
