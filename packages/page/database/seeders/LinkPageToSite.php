<?php

declare(strict_types=1);

namespace Kelnik\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Page\Models\Page;

final class LinkPageToSite extends Seeder
{
    public function run(): void
    {
        /** @var Site $primarySite */
        $primarySite = resolve(SiteService::class)->findPrimary();

        if (!$primarySite->exists) {
            return;
        }

        Page::query()->update(['site_id' => $primarySite->getKey()]);
    }
}
