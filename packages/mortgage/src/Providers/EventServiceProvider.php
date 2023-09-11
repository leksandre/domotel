<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Providers;

use Kelnik\Mortgage\Events\BankEvent;
use Kelnik\Mortgage\Events\ProgramEvent;
use Kelnik\Mortgage\Listeners\ResetBankCache;
use Kelnik\Mortgage\Models\Bank;
use Kelnik\Mortgage\Models\Program;
use Kelnik\Mortgage\Observers\BankObserver;
use Kelnik\Mortgage\Observers\ProgramObserver;

final class EventServiceProvider extends \Illuminate\Foundation\Support\Providers\EventServiceProvider
{
    protected $listen = [
        BankEvent::class => [
            ResetBankCache::class
        ],
        ProgramEvent::class => [
            ResetBankCache::class
        ]
    ];

    public function boot(): void
    {
        Bank::observe(BankObserver::class);
        Program::observe(ProgramObserver::class);
    }
}
