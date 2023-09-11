<?php

namespace Kelnik\Page\Services\Contracts;

use Kelnik\Page\Models\Contracts\BufferDto;

interface PageComponentBuffer
{
    public function add(BufferDto $dto): void;

    public function get(string $bufferName): ?BufferDto;

    public function pull(string $bufferName): ?BufferDto;

    /**
     * @param string|string[] $bufferName
     * @return void
     */
    public function remove(string|array $bufferName): void;

    public function reset(): void;
}
