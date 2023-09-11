<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Models;

final class Button implements Contracts\Button
{
    public const EXTERNAL_TARGET = '_blank';

    private int|string $formKey;
    private string $text;
    private string $target = '_self';

    public function __construct(int|string $formKey, string $text, ?string $target = null)
    {
        $this->formKey = $formKey;
        $this->text = $text;

        if (!is_null($target)) {
            $this->target = $target;
        }
    }

    public function getFormKey(): int|string
    {
        return $this->formKey;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function toArray(): array
    {
        return ['formKey' => $this->formKey, 'text' => $this->text, 'target' => $this->target];
    }
}
