<?php

declare(strict_types=1);

namespace Kelnik\Form\Providers;

use Kelnik\Form\Events\FieldEvent;
use Kelnik\Form\Events\FormEvent;
use Kelnik\Form\Events\LogAddedEvent;
use Kelnik\Form\Listeners\ResetFormCache;
use Kelnik\Form\Listeners\SendNotifyOnNewLog;
use Kelnik\Form\Models\Field;
use Kelnik\Form\Models\Form;
use Kelnik\Form\Observers\FieldObserver;
use Kelnik\Form\Observers\FormObserver;

final class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    protected $listen = [
        FormEvent::class => [
            ResetFormCache::class
        ],
        FieldEvent::class => [
            ResetFormCache::class
        ],
        LogAddedEvent::class => [
            SendNotifyOnNewLog::class
        ]
    ];

    public function boot(): void
    {
        parent::boot();

        Form::observe(FormObserver::class);
        Field::observe(FieldObserver::class);
    }
}
