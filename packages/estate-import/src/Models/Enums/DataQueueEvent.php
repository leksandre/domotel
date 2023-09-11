<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Enums;

enum DataQueueEvent: int
{
    case UnProcessed = 0;
    case Added = 1;
    case Declined = 4;
    case Deleted = 3;
    case Failed = 5;
    case Updated = 2;
}
