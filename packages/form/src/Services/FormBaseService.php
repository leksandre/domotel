<?php

declare(strict_types=1);

namespace Kelnik\Form\Services;

class FormBaseService implements Contracts\FormBaseService
{
    public function getCacheTag(int|string $id): ?string
    {
        return 'form_' . $id;
    }
}
