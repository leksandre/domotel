<?php

declare(strict_types=1);

namespace Kelnik\Document\Dto;

use App\Models\User;
use Kelnik\Core\Dto\Contracts\BaseDto;

final readonly class GroupDto extends BaseDto
{
    public function __construct(
        public string $title,
        public bool $active,
        public User $user
    ) {
    }
}
