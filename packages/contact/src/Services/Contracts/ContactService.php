<?php

declare(strict_types=1);

namespace Kelnik\Contact\Services\Contracts;

use Illuminate\Support\Collection;
use Orchid\Screen\Field;

interface ContactService
{
    public function getContentLink(): Field;

    public function getOffices(): Collection;

    public function getOfficeCacheTag(): string;

    public function getSocials(): Collection;

    public function getSocialCacheTag(): string;
}
