<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Core\Providers;

use Illuminate\Database\Eloquent\Model;

final class TestModel extends Model
{
    public $incrementing = false;
    public $timestamps = false;
}
