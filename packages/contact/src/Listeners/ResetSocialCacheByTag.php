<?php

declare(strict_types=1);

namespace Kelnik\Contact\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Contact\Events\SocialLinkEvent;
use Kelnik\Contact\Services\Contracts\ContactService;

final class ResetSocialCacheByTag implements ShouldQueue
{
    public function handle(SocialLinkEvent $event): void
    {
        Cache::tags(resolve(ContactService::class)->getSocialCacheTag())->flush();
    }

    public function uniqueId(): string
    {
        return 'contact_social';
    }
}
