<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Models\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface Button extends Arrayable
{
    public function getFormKey(): int|string;

    public function getText(): string;

    public function getTarget(): string;
}
