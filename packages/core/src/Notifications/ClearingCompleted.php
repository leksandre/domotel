<?php

namespace Kelnik\Core\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Orchid\Platform\Notifications\DashboardChannel;
use Orchid\Platform\Notifications\DashboardMessage;

final class ClearingCompleted extends Notification
{
    use Queueable;

    public function __construct(private readonly int $successCnt = 0, private readonly int $errorCnt = 0)
    {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return [DashboardChannel::class];
    }

    public function toDashboard($notifiable)
    {
        return (new DashboardMessage())
            ->title(trans('kelnik-core::admin.tools.clearing.notify.title'))
            ->message(
                trans(
                    'kelnik-core::admin.tools.clearing.notify.message',
                    ['success' => $this->successCnt, 'error' => $this->errorCnt]
                )
            );
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
