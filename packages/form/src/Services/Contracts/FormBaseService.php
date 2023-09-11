<?php

declare(strict_types=1);

namespace Kelnik\Form\Services\Contracts;

interface FormBaseService
{
    public function getCacheTag(int|string $id): ?string;
}
