<?php

declare(strict_types=1);

namespace Kelnik\Core\Dto;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Kelnik\Core\Dto\Contracts\BaseDto;

final readonly class ClearingDto extends BaseDto
{
    public function __construct(
        /** @var string[] */
        public array $modules,
        public User $user,
        /** @var class-string<Notification> */
        public ?string $notification = null
    ) {
    }
}
