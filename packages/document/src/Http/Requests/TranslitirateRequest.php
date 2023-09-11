<?php

declare(strict_types=1);

namespace Kelnik\Document\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Kelnik\Document\Dto\TranslitirateDto;

final class TranslitirateRequest extends FormRequest
{
    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'action' => [
                'required',
                Rule::in(['check', 'transliterate'])
            ],
            'source' => 'required',
            'sourceId' => 'required|integer',
            'slug' => 'nullable|'
        ];
    }

    public function getDto(): TranslitirateDto
    {
        return new TranslitirateDto(
            $this->input('action'),
            $this->input('source'),
            $this->integer('sourceId'),
            $this->input('slug')
        );
    }
}
