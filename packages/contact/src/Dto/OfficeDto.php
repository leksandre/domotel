<?php

declare(strict_types=1);

namespace Kelnik\Contact\Dto;

use Kelnik\Core\Dto\Contracts\BaseDto;
use Kelnik\Core\Map\Contracts\Coords;

final readonly class OfficeDto extends BaseDto
{
    public function __construct(
        public string $title,
        public bool $active,
        public Coords $coords,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $region = null,
        public ?string $city = null,
        public ?string $street = null,
        public ?string $route_link = null,
        public ?int $image_id = null,
        public array $schedule = []
    ) {
    }
}
