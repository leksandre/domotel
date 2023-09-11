<?php

declare(strict_types=1);

namespace Kelnik\Progress\Dto;

use App\Models\User;
use Kelnik\Core\Dto\Contracts\BaseDto;
use Kelnik\Progress\Models\Group;

final readonly class CameraDto extends BaseDto
{
    public function __construct(
        public string $title,
        public string $url,
        public bool $active,
        public ?string $description,
        public ?int $cover_image,
        public ?Group $group,
        public User $user
    ) {
    }
}
