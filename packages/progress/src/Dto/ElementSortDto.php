<?php

declare(strict_types=1);

namespace Kelnik\Progress\Dto;

use Kelnik\Core\Dto\Contracts\BaseDto;

final readonly class ElementSortDto extends BaseDto
{
    /** @var int[] */
    public array $elements;
    public int $defaultPriority;

    public function __construct(array $elements, ?int $defaultPriority = null)
    {
        $this->elements = $elements;
        $this->defaultPriority = $defaultPriority ?? 500;
    }
}
