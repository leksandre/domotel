<?php

declare(strict_types=1);

namespace Kelnik\Form\Fields\Contracts;

interface FieldTypeAttributes
{
    public function setAttribute(string $name, $value): void;

    public function getAttribute(string $name);
}
