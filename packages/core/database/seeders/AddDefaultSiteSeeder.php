<?php

declare(strict_types=1);

namespace Kelnik\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Kelnik\Core\Models\Enums\Lang;
use Kelnik\Core\Models\Enums\Type;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Models\SiteSettings;

final class AddDefaultSiteSeeder extends Seeder
{
    public function run(): void
    {
        (new Site([
            'title' => trans('kelnik-core::seeder.defaultTitle'),
            'type' => Type::Site->value,
            'locale' => (Lang::tryFrom(config('app.locale')) ?? Lang::Russian)->value,
            'active' => true,
            'primary' => true,
            'settings' => new SiteSettings()
        ]))->saveQuietly();
    }
}
