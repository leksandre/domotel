<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Core\Http\Controllers;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;

final class SeoRobotsControllerTest extends TestCase
{
    use RefreshDatabase;
    use SiteTrait;

    public function testRequestOnDefaultReturnValidContent()
    {
        $res = $this->get('/robots.txt');

        $res->assertOk();
        $res->assertHeader('content-type', 'text/plain; charset=utf-8');
        $res->assertContent(config('kelnik-core.site.settings.seo.robots'));
    }

    public function testRequestOnSiteWithDefaultValuesReturnValidContent()
    {
        $this->initSite();

        $res = $this->get('/robots.txt');

        $res->assertOk();
        $res->assertHeader('content-type', 'text/plain; charset=utf-8');
        $res->assertContent('');
    }

    public function testRequestOnSiteReturnValidContent()
    {
        $this->initSite();

        $faker = Factory::create(config('app.faker_locale'));
        $content = $faker->sentence();
        $this->site->settings->setSeoRobots($content);
        $res = $this->get('/robots.txt');

        $res->assertOk();
        $res->assertHeader('content-type', 'text/plain; charset=utf-8');
        $res->assertContent($content);
    }
}
