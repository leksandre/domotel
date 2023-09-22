<?php

declare(strict_types=1);

namespace Kelnik\Progress\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Kelnik\Progress\Dto\AlbumDto;
use Kelnik\Progress\Models\Group;
use Kelnik\Progress\Rules\ProgressAlbumVideo;

final class AlbumSaveRequest extends FormRequest
{
    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'album.title' => 'required|max:255',
            'album.active' => 'boolean',
            'album.publish_date' => 'required|date',
            'album.description' => 'nullable|max:400',
            'album.group_id' => 'nullable|numeric',
            'videos' => 'nullable|array',
            'videos.*.id' => 'nullable|numeric',
            'videos.*.url' => [
                'required',
                'url',
                new ProgressAlbumVideo()
            ],
        ];
    }

    public function getDto(): AlbumDto
    {
        $groupId = $this->integer('album.group_id') ?: null;
        $group = null;

        if ($groupId) {
            $group = new Group();
            $group->id = $groupId;
        }

        return new AlbumDto(
            $this->string('album.title')->toString(),
            $this->boolean('album.active'),
            $this->date('album.publish_date'),
            $this->string('album.description')->toString(),
            $this->string('album.comment')->toString(),
            $this->input('album.images') ?? [],
            $this->input('videos') ?? [],
            $group,
            $this->user()
        );
    }
}
