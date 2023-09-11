<?php

declare(strict_types=1);

namespace Kelnik\Page\Models\Contracts;

use Illuminate\Support\Collection;

interface DataProvider
{
    /**
     * Return self or page data
     */
    public function getValue(): Collection;

    public function get(string $key): mixed;

    public function setValue(Collection $data): void;

    public function put(string $key, $value): void;
}
