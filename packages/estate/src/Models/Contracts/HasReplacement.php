<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models\Contracts;

interface HasReplacement
{
    public function getReplacementField(): string;
}
