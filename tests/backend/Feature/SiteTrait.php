<?php

namespace Kelnik\Tests\Feature;

use Kelnik\Core\Database\Seeders\AddDefaultSiteSeeder;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Services\Contracts\SiteService;

trait SiteTrait
{
    protected Site $site;

    protected function initSite(): void
    {
        $this->seed(AddDefaultSiteSeeder::class);
        $this->site = resolve(SiteService::class)->findPrimary();
    }
}
