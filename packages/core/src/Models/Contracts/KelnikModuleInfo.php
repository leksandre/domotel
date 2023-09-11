<?php

declare(strict_types=1);

namespace Kelnik\Core\Models\Contracts;

interface KelnikModuleInfo
{
    /** @return class-string */
    public function getProvider(): string;

    public function getName(): string;

    public function getTitle(): string;

    public function getVersion(): string;

    public function hasCleaner(): bool;

    /** @return array<string, string> */
    public function getComponents(): array;
}
