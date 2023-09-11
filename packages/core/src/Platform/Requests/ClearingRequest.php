<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Kelnik\Core\Dto\ClearingDto;
use Kelnik\Core\Notifications\ClearingCompleted;

final class ClearingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'modules' => 'required|array',
            'modules.*' => 'required'
        ];
    }

    public function getDto(): ClearingDto
    {
        return new ClearingDto(
            Arr::get($this->safe()->only('modules'), 'modules') ?? [],
            $this->user(),
            ClearingCompleted::class
        );
    }
}
