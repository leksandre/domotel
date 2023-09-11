<?php

declare(strict_types=1);

namespace Kelnik\Core\Events\Contracts;

abstract class ModelEvent
{
    public const CREATED = 'created';
    public const UPDATED = 'updated';
    public const RESTORED = 'restored';
    public const DELETED = 'deleted';
    public const FORCE_DELETED = 'forceDeleted';
}
