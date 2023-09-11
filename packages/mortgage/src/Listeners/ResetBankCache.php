<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Listeners;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Kelnik\Mortgage\Events\BankEvent;
use Kelnik\Mortgage\Events\ProgramEvent;
use Kelnik\Mortgage\Services\Contracts\MortgageService;

final class ResetBankCache implements ShouldQueue, ShouldBeUnique
{
    private BankEvent|ProgramEvent $event;
    private MortgageService $mortgageService;

    public function __construct()
    {
        $this->mortgageService = resolve(MortgageService::class);
    }

    public function handle(BankEvent|ProgramEvent $event): void
    {
        $this->event = $event;

        if ($this->isBankEvent()) {
            $this->handleBankEvent();
            return;
        }

        $this->handleProgramEvent();
    }

    private function handleBankEvent(): void
    {
        if ($this->event->event === $this->event::CREATED) {
            Cache::tags($this->mortgageService->getBankListCacheTag())->flush();

            return;
        }

        $tags = [
            $this->mortgageService->getBankCacheTag($this->event->bank->id)
        ];

        if ($this->isBankEvent() && $this->event->bank->isDirty('active')) {
            $tags[] = $this->mortgageService->getBankListCacheTag();
        }

        Cache::tags($tags)->flush();
    }

    private function handleProgramEvent(): void
    {
        Cache::tags([
            $this->mortgageService->getBankCacheTag($this->event->program->bank_id)
        ])->flush();
    }

    private function isBankEvent(): bool
    {
        return $this->event instanceof BankEvent;
    }

    public function uniqueId(): int
    {
        return $this->isBankEvent()
            ? $this->event->bank->getKey()
            : $this->event->program->bank_id;
    }
}
