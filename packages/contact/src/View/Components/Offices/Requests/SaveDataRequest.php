<?php

declare(strict_types=1);

namespace Kelnik\Contact\View\Components\Offices\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SaveDataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'data.content.title' => 'nullable|max:255',
            'data.content.alias' => 'nullable|max:150|regex:/[a-z0-9\-_]+/i',
            'data.map.zoom' => 'required|integer|min:0|max:16'
        ];
    }

    /**
     * @return array{
     *  content: array{title: ?string, alias: ?string},
     *  map: array{zoom: int}
     * }
     */
    public function getData(): array
    {
        return [
            'content' => [
                'title' => $this->string('data.content.title')->toString(),
                'alias' => $this->string('data.content.alias')->toString()
            ],
            'map' => [
                'zoom' => $this->integer('data.map.zoom')
            ]
        ];
    }
}
