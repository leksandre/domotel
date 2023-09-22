<?php

declare(strict_types=1);

namespace Kelnik\Progress\Dto;

use App\Models\User;
use Illuminate\Support\Carbon;
use Kelnik\Core\Dto\Contracts\BaseDto;
use Kelnik\Progress\Models\Group;

final readonly class AlbumDto extends BaseDto
{
    /**
     * @param string $title
     * @param bool $active
     * @param Carbon|null $publish_date
     * @param string|null $description
     * @param int[] $images
     * @param array<int, array{id?:int, url:string}> $videos
     * @param Group|null $group
     * @param User $user
     */
    public function __construct(
        public string $title,
        public bool $active,
        public ?Carbon $publish_date,
        public ?string $description,
        public ?string $comment,
        public array $images,
        public array $videos,
        public ?Group $group,
        public User $user
    ) {
    }
}
