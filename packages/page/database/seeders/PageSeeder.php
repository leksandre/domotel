<?php

declare(strict_types=1);

namespace Kelnik\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Page\Models\Page;

final class PageSeeder extends Seeder
{
    public function __construct(private ?Site $site = null)
    {
        $this->site ??= resolve(SiteService::class)->findPrimary();
    }

    public function run(): void
    {
        Page::factory()->createQuietly([
            'site_id' => (int)($this->site?->getKey()),
            'active' => true,
            'slug' => null,
            'title' => trans('kelnik-page::seeder.home', [], $this->site?->locale->value),
            'path' => hash('md5', '')
        ]);
    }
}
