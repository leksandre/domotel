<?php

declare(strict_types=1);

namespace Kelnik\Form\Fields\Additional;

use Illuminate\Support\Arr;
use Kelnik\Form\Fields\Contracts\FieldDataProvider;
use Kelnik\Form\Fields\FormField;
use Symfony\Component\HttpFoundation\FileBag;

final class AdditionalField extends FormField
{
    public const CSS_CLASS = 'j-form__fill-field';

    protected string $template = 'kelnik-form::fields.additional';

    public function setAttributes(): void
    {
        $this->setAttribute('name', $this->formName . '[' . $this->name . ']');
        $this->setAttribute('id', $this->getId());
        $this->setAttribute('class', self::CSS_CLASS);
    }

    public function validate(array $data, FileBag $files): bool|array
    {
        return true;
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
        return trans('kelnik-form::fields.additional.title');
    }
}
