<?php

declare(strict_types=1);

namespace Kelnik\Tests\Unit\Core\Services;

use Kelnik\Core\Services\CoreService;
use Kelnik\Tests\TestCase;

final class CoreServiceTest extends TestCase
{
    public function testRouteName()
    {
        $routeName = 'page.list';
        $resRouteName = (new CoreService())->getFullRouteName($routeName);

        $this->assertTrue($resRouteName === config('kelnik-core.routeNamePrefix.platform') . $routeName);
    }
}
