<?php

declare(strict_types=1);

namespace Kelnik\Contact\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Contact\Events\OfficeEvent;
use Kelnik\Contact\Services\Contracts\ContactService;

final class ResetOfficeCacheByTag implements ShouldQueue
{
    public function handle(OfficeEvent $event): void
    {
        Cache::tags(resolve(ContactService::class)->getOfficeCacheTag())->flush();
    }

    public function uniqueId(): string
    {
        return 'contact_office';
    }
}
