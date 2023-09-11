<?php

declare(strict_types=1);

namespace Kelnik\Form\Fields\Textarea;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Kelnik\Form\Fields\Contracts\FieldDataProvider;
use Kelnik\Form\Fields\FormField;
use Symfony\Component\HttpFoundation\FileBag;

final class TextareaField extends FormField
{
    public const VALUE_MAX_LENGTH = 100;

    protected string $template = 'kelnik-form::fields.textarea';

    public function setAttributes(): void
    {
        $this->setAttribute('name', $this->formName . '[' . $this->name . ']');
        $this->setAttribute('id', $this->getId());
        $this->setAttribute('aria-label', $this->title);

        if (!empty($this->params['attributes']['placeholder'])) {
            $this->setAttribute('placeholder', $this->params['attributes']['placeholder']);
        }

        if (!empty($this->params['attributes']['required'])) {
            $this->setAttribute('data-validate-required', 'true');
        }

        $this->setAttribute('data-validate-name', $this->name);
        $this->setAttribute('data-validate-required-ms', trans('kelnik-form::fields.textarea.requiredMsg'));
        $this->setAttribute('data-validate-max', self::VALUE_MAX_LENGTH);
        $this->setAttribute('data-validate-max-msg', trans('kelnik-form::fields.textarea.maxMsg'));
    }

    public function validate(array $data, FileBag $files): bool|array
    {
        if (!$this->isRequired()) {
            return true;
        }

        $validator = Validator::make(
            $data,
            [$this->name => 'required'],
            [],
            [$this->name => $this->title]
        );

        return $validator->passes() ?: $validator->errors()->toArray();
    }

    public function process(array $data, FileBag $files): string
    {
        return trim((string)Arr::get($data, $this->name));
    }

    public static function initDataProvider(): FieldDataProvider
    {
        return new DataProvider(self::class);
    }

    public static function getTypeTitle(): string
    {
        return trans('kelnik-form::fields.textarea.title');
    }
}
