<?php

declare(strict_types=1);

namespace Kelnik\Core\Models\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface SiteSettings extends Arrayable
{
    public function __construct(array $data);

    public function setSeoRobots(string $content): void;

    public function getSeoRobots(): string;
}
