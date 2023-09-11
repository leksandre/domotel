<?php

declare(strict_types=1);

namespace Kelnik\Form\Fields\Email;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Kelnik\Form\Fields\Contracts\FieldDataProvider;
use Kelnik\Form\Fields\FormField;
use Symfony\Component\HttpFoundation\FileBag;

final class EmailField extends FormField
{
    protected string $template = 'kelnik-form::fields.input';

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
        $this->setAttribute('data-validate-required-ms', trans('kelnik-form::fields.phone.requiredMsg'));
        $this->setAttribute('data-validate-email', '');
    }

    public function validate(array $data, FileBag $files): bool|array
    {
        $validator = Validator::make(
            $data,
            [
                $this->name => [$this->isRequired() ? 'required' : 'nullable', 'email']
            ],
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
        return trans('kelnik-form::fields.email.title');
    }
}
