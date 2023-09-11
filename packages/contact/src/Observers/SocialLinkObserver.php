<?php

declare(strict_types=1);

namespace Kelnik\Contact\Observers;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Contact\Events\SocialLinkEvent;
use Kelnik\Contact\Models\SocialLink;
use Kelnik\Contact\Observers\Contracts\Observer;

final class SocialLinkObserver extends Observer
{
    /**
     * @param SocialLink $model
     * @param string $event
     * @return void
     */
    protected function handle(Model $model, string $event): void
    {
        SocialLinkEvent::dispatch($model, $event);
    }
}
