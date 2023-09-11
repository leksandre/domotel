<?php

declare(strict_types=1);

namespace Kelnik\Page\Models\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Orchid\Attachment\Models\Attachment;

interface PageMeta extends Arrayable
{
    public function __construct(int|string $pageId, array $data = []);

    public function fill(array $data): void;

    public function setTitle(?string $value): void;

    public function getTitle(): ?string;

    public function setDescription(?string $value): void;

    public function getDescription(): ?string;

    public function setKeywords(?string $value): void;

    public function getKeywords(): ?string;

    public function setImage(null|int|Attachment $value): void;

    public function getImage(): ?Attachment;

    public function deleteImage(): bool;
}
