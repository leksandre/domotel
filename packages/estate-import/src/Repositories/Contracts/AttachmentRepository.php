<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories\Contracts;

use Illuminate\Support\LazyCollection;

interface AttachmentRepository extends BaseLazyCollection
{
    public function getLazyCollection(): LazyCollection;
}
