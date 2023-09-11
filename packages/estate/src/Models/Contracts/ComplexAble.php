<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models\Contracts;

interface ComplexAble
{
    public function getComplex(): ?EstateModel;
}
