<?php

declare(strict_types=1);

namespace Kelnik\Progress\View\Components\Progress\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SaveDataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'data.content.title' => 'nullable|max:255',
            'data.content.text' => 'nullable|string',
            'data.content.alias' => 'nullable|max:150|regex:/[a-z0-9\-_]+/i',
            'data.content.deadlines' => 'nullable|array',
            'data.content.deadlines.*.title' => 'required|max:255',
            'data.content.deadlines.*.text' => 'required|max:255',
            'data.content.group' => 'nullable|integer',
            'data.content.buttonText' => 'nullable|string'
        ];
    }

    /**
     * @return array{
     *  title: ?string,
     *  text: ?string,
     *  alias: ?string,
     *  deadlines: array,
     *  group: ?int,
     *  buttonText: ?string
     * }
     */
    public function getData(): array
    {
        return [
            'title' => $this->string('data.content.title')->toString(),
            'text' => $this->string('data.content.title')->toString(),
            'alias' => $this->string('data.content.alias')->toString(),
            'deadlines' => $this->getDeadLines(),
            'buttonText' => $this->string('data.content.buttonText')->toString(),
            'group' => $this->integer('data.content.group') ?: null
        ];
    }

    private function getDeadLines(): array
    {
        $deadlines = $this->input('data.content.deadlines') ?? [];

        if (!$deadlines) {
            return $deadlines;
        }

        array_walk(
            $deadlines,
            static function (&$el, $index) use (&$deadlines) {
                foreach (['title', 'text'] as $field) {
                    $el[$field] = trim($el[$field] ?? '');
                }

                if (!mb_strlen($el['title']) && !mb_strlen($el['text'])) {
                    unset($el, $deadlines[$index]);
                }
            }
        );

        return array_values($deadlines);
    }
}
