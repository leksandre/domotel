<?php

declare(strict_types=1);

namespace Kelnik\Core\Models;

final class KelnikModuleInfo implements Contracts\KelnikModuleInfo
{
    public function __construct(
        private readonly string $provider,
        private readonly string $name,
        private readonly string $title,
        private readonly string $version,
        private readonly bool $hasCleaner,
        private readonly array $components,
    ) {
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function hasCleaner(): bool
    {
        return $this->hasCleaner;
    }

    public function getComponents(): array
    {
        return $this->components;
    }
}
