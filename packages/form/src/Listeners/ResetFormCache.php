<?php

declare(strict_types=1);

namespace Kelnik\Form\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Form\Events\FieldEvent;
use Kelnik\Form\Events\FormEvent;
use Kelnik\Form\Services\Contracts\FormBaseService;

final class ResetFormCache implements ShouldQueue
{
    public function handle(FormEvent|FieldEvent $event): void
    {
        $isForm = $event instanceof FormEvent;

        if ($isForm && $event->methodName === $event::CREATED) {
            return;
        }

        if (!$isForm && $event->methodName === $event::CREATED && !$event->field->active) {
            return;
        }

        Cache::tags(
            resolve(FormBaseService::class)->getCacheTag($isForm ? $event->form->getKey() : $event->field->form_id)
        )->flush();
    }
}
