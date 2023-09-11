<?php

declare(strict_types=1);

namespace Kelnik\Form\Services\Contracts;

use Illuminate\Http\Request;

interface FormService
{
    public function __construct(int|string $primary);

    public function build(): array;

    public function submit(Request $request): bool;

    public function getLastErrors(): array;
}
