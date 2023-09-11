<?php

declare(strict_types=1);

namespace Kelnik\Form\Fields;

use Illuminate\Support\Facades\View;
use Kelnik\Form\Fields\Contracts\FieldType;
use Kelnik\Form\Fields\Contracts\FieldTypeAttributes;

abstract class FormField implements FieldType, FieldTypeAttributes
{
    public const FIELD_PREFIX = 'field';

    protected array $attributes = [];
    protected string $template;

    public function __construct(
        protected string $formName,
        protected string $name,
        protected string $title,
        protected array $params = []
    ) {
        $this->setAttributes();
    }

    public function getId(): string
    {
        return 'id-' . md5($this->name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function getTemplateData(): array
    {
        $attrs = [];

        foreach ($this->attributes as $k => $v) {
            $attrs[] = $k . '="' . $v . '"';
        }

        $res = [
            'id' => $this->getId(),
            'name' => $this->name,
            'title' => $this->title,
            'attributes' => implode(' ', $attrs)
        ];

        foreach ($this->params as $k => $v) {
            if ($k === 'attributes' || isset($res[$k])) {
                continue;
            }

            $res[$k] = $v;
        }

        return $res;
    }

    public function isRequired(): bool
    {
        return !empty($this->params['attributes']['required']);
    }

    public function setAttributes()
    {
    }

    public function setAttribute(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function getAttribute(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function render(): string
    {
        return View::make($this->template, $this->getTemplateData())->render();
    }
}
