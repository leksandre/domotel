<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Core\Services;

use Exception;
use Kelnik\Core\Models\KelnikModuleInfo;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Services\CoreService;
use Kelnik\Tests\TestCase;

final class CoreServiceTest extends TestCase
{
    /** @throws Exception */
    public function testModuleList()
    {
        $app = app();
        $provider = $app->getProviders(CoreServiceProvider::class);
        $provider = current($provider);

        if (!$provider) {
            throw new Exception('Core service provider not found');
        }

        $modules = [];
        foreach ($provider->getModules() as $module) {
            $modules[] = $module->getName();
        }
        unset($provider);
        $serviceModules = (new CoreService())->getModuleList()->map(fn(KelnikModuleInfo $el) => $el->getName());

        $this->assertTrue($modules === $serviceModules->toArray());
    }

    public function testRouteName()
    {
        $routeName = 'page.list';
        $resRouteName = (new CoreService())->getFullRouteName($routeName);

        $this->assertTrue($resRouteName === config('kelnik-core.routeNamePrefix.platform') . $routeName);
    }
}
