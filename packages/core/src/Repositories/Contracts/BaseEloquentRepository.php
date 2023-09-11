<?php

declare(strict_types=1);

namespace Kelnik\Core\Repositories\Contracts;

abstract class BaseEloquentRepository
{
    /** @var class-string */
    protected $model;
}
