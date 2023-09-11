<?php

namespace Kelnik\EstateImport\Repositories\Contracts;

use Illuminate\Support\LazyCollection;

interface BaseLazyCollection
{
    public function getLazyCollection(): LazyCollection;
}
