<?php

declare(strict_types=1);

namespace Kelnik\Document\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Kelnik\Document\Dto\ElementSortDto;

final class ElementSortRequest extends FormRequest
{
    /** @return array<string, string> */
    public function rules(): array
    {
        return [
            'elements' => 'required|array',
            'elements.*' => 'integer'
        ];
    }

    public function getDto(): ElementSortDto
    {
        $elements = Arr::get($this->safe()->only('elements'), 'elements');

        foreach ($elements as $k => &$v) {
            $v = (int)$v;
            if (!$v) {
                unset($elements[$k]);
            }
        }

        return new ElementSortDto($elements);
    }
}
