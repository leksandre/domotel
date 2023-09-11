<?php

declare(strict_types=1);

namespace Kelnik\Form\Fields\Phone;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Kelnik\Form\Fields\Contracts\FieldDataProvider;
use Kelnik\Form\Fields\FormField;
use Symfony\Component\HttpFoundation\FileBag;

final class PhoneField extends FormField
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
        $this->setAttribute('data-validate-mask', '+ 7 (999) 999-99-99');
        $this->setAttribute('data-validate-mask-msg', trans('kelnik-form::fields.phone.maskMsg'));
        $this->setAttribute('data-validate-required-msg', trans('kelnik-form::fields.phone.requiredMsg'));
    }

    public function validate(array $data, FileBag $files): bool|array
    {
        $validator = Validator::make(
            $data,
            [
                $this->name => [$this->isRequired() ? 'required' : 'nullable', 'max:50', 'regex:/^\+?[0-9()\- ]+$/i']
            ],
            [],
            [$this->name => $this->title]
        );

        return $validator->passes() ?: $validator->errors()->toArray();
    }

    public function process(array $data, FileBag $files): string
    {
        return trim(
            preg_replace('![^0-9()\-+ ]!i', '', (string)Arr::get($data, $this->name))
        );
    }

    public static function initDataProvider(): FieldDataProvider
    {
        return new DataProvider(self::class);
    }

    public static function getTypeTitle(): string
    {
        return trans('kelnik-form::fields.phone.title');
    }
}
