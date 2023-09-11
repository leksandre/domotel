<?php

declare(strict_types=1);

namespace Kelnik\Contact\Dto;

use Kelnik\Core\Dto\Contracts\BaseDto;

final readonly class SocialDto extends BaseDto
{
    public function __construct(
        public string $title,
        public string $link,
        public bool $active,
        public ?int $icon_id = null
    ) {
    }
}
