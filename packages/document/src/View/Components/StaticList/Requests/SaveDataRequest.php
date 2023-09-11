<?php

declare(strict_types=1);

namespace Kelnik\Document\View\Components\StaticList\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SaveDataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'data.content.title' => 'nullable|max:255',
            'data.content.alias' => 'nullable|max:150|regex:/[a-z0-9\-_]+/i',
            'data.content.group' => 'nullable|integer'
        ];
    }

    /**
     * @return array{
     *  title: ?string,
     *  alias: ?string,
     *  group: ?int
     * }
     */
    public function getData(): array
    {
        return [
            'title' => $this->string('data.content.title')->toString(),
            'alias' => $this->string('data.content.alias')->toString(),
            'group' => $this->integer('data.content.group') ?: null
        ];
    }
}
