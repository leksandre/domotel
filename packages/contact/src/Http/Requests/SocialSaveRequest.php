<?php

declare(strict_types=1);

namespace Kelnik\Contact\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Kelnik\Contact\Dto\SocialDto;

final class SocialSaveRequest extends FormRequest
{
    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'social.title' => 'required|max:255',
            'social.active' => 'boolean',
            'social.link' => 'max:255',
            'social.icon_id' => 'nullable|integer'
        ];
    }

    public function getDto(): SocialDto
    {
        $data = Arr::get($this->safe()->only('social'), 'social');
        $data['active'] = $this->boolean('social.active');

        if (!empty($data['icon_id'])) {
            $data['icon_id'] = (int)$data['icon_id'];
        }

        return new SocialDto(...$data);
    }
}
