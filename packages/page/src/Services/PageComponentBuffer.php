<?php

declare(strict_types=1);

namespace Kelnik\Page\Services;

use Exception;
use Illuminate\Support\Collection;
use Kelnik\Page\Models\Contracts\BufferDto;

final class PageComponentBuffer implements Contracts\PageComponentBuffer
{
    private static ?self $instance = null;
    private Collection $buffer;

    private function __construct()
    {
        $this->buffer = new Collection();
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
        throw new Exception('Cannot unserialize singleton');
    }

    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function add(BufferDto $dto): void
    {
        $this->buffer->put($dto::class, $dto);
    }

    public function get(string $bufferName): ?BufferDto
    {
        return $this->buffer->get($bufferName);
    }

    public function pull(string $bufferName): ?BufferDto
    {
        return $this->buffer->pull($bufferName);
    }

    public function remove(string|array $bufferName): void
    {
        $this->buffer->forget($bufferName);
    }

    public function reset(): void
    {
        $this->buffer = new Collection();
    }
}
