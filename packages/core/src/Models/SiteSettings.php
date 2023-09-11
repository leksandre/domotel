<?php

declare(strict_types=1);

namespace Kelnik\Core\Models;

use Illuminate\Support\Arr;
use Kelnik\Core\Services\Contracts\SettingsService;

final class SiteSettings implements Contracts\SiteSettings
{
    private array $data;
    private readonly SettingsService $settingsService;

    public function __construct(array $data = [])
    {
        $this->data = $data;
        $this->settingsService = resolve(SettingsService::class);
    }

    public function setSeoRobots(string $content): void
    {
        $this->data['seo']['robots'] = $content;
    }

    public function getSeoRobots(): string
    {
        return Arr::get($this->data, 'seo.robots', '');
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
