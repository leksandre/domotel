<?php

declare(strict_types=1);

namespace Kelnik\Form\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Kelnik\Form\Events\LogAddedEvent;
use Kelnik\Form\Mail\FormRequest;
use Kelnik\Form\Models\Email;
use Throwable;

final class SendNotifyOnNewLog
{
    public function handle(LogAddedEvent $event): void
    {
        /** @var Email $entry */
        foreach ($event->formLog->form->emails as $entry) {
            try {
                Mail::to($entry->email)->queue(new FormRequest($event->formLog));
            } catch (Throwable $e) {
                Log::error($e->getMessage(), ['email' => $entry->email]);
            }
        }
    }
}
