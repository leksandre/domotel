<?php

declare(strict_types=1);

namespace Kelnik\Core\Theme\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Orchid\Attachment\Models\Attachment;
use Stringable;

interface Font extends Arrayable, JsonSerializable, Stringable
{
    public function isLoaded(): bool;

    public function setFileModel(Attachment $fileModel): void;

    public function getFullPath(): ?string;

    public function getUrl(): ?string;

    public function getExtension(): ?string;

    public function setActive(bool $active): void;

    public function isActive(): bool;

    public function delete(): ?bool;
}
