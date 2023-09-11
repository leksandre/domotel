<?php

declare(strict_types=1);

namespace Kelnik\Document\Dto;

use Kelnik\Core\Dto\Contracts\BaseDto;

final readonly class TranslitirateDto extends BaseDto
{
    public function __construct(
        public string $action,
        public string $source,
        public int $sourceId,
        public ?string $slug
    ) {
    }
}
