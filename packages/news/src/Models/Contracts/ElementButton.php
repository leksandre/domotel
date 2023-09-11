<?php

declare(strict_types=1);

namespace Kelnik\News\Models\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface ElementButton extends Arrayable
{
    public const EXTERNAL_TARGET = '_blank';

    public function getLink(): string;

    public function getText(): string;

    public function getTarget(): string;
}
