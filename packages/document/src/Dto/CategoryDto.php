<?php

declare(strict_types=1);

namespace Kelnik\Document\Dto;

use App\Models\User;
use Illuminate\Support\Carbon;
use Kelnik\Core\Dto\Contracts\BaseDto;
use Kelnik\Document\Models\Group;

final readonly class CategoryDto extends BaseDto
{
    /**
     * @param string $title
     * @param bool $active
     * @param Group|null $group
     * @param User $user
     * @param string|null $slug
     * @param array<int, array{
     *    id?: int,
     *    active: bool,
     *    title: string,
     *    author: ?string,
     *    publish_date: ?Carbon,
     *    attachment_id: ?int
     *   }> $elements
     */
    public function __construct(
        public string $title,
        public bool $active,
        public ?Group $group,
        public User $user,
        public ?string $slug = null,
        public array $elements = []
    ) {
    }
}
