<?php

declare(strict_types=1);

namespace Kelnik\Form\Fields\Contracts;

use Illuminate\Http\Request;
use Kelnik\Form\Models\Field;

interface FieldDataProvider
{
    public function getEditLayouts(): array;

    public function validateRequest(Field $field, Request $request);

    public function setDataFromRequest(Field &$field, Request $request);
}
