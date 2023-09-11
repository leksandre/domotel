<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Processor\Contracts;

abstract class Processor extends BaseProcessor
{
    abstract public function execute(): bool;
}
