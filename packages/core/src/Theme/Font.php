<?php

declare(strict_types=1);

namespace Kelnik\Core\Theme;

use Orchid\Attachment\Models\Attachment;

final class Font implements Contracts\Font
{
    public function __construct(
        private ?Attachment $fileModel,
        private bool $active = false,
        private ?string $title = null
    ) {
    }

    public function isLoaded(): bool
    {
        return $this->fileModel instanceof Attachment;
    }

    public function setFileModel(Attachment $fileModel): void
    {
        $this->fileModel = $fileModel;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getFullPath(): ?string
    {
        return $this->fileModel?->physicalPath();
    }

    public function getUrl(): ?string
    {
        return $this->fileModel?->url;
    }

    public function getFileName(): ?string
    {
        return $this->fileModel?->title;
    }

    public function getExtension(): ?string
    {
        return $this->isLoaded()
                ? strtolower($this->fileModel->extension)
                : null;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function delete(): ?bool
    {
        return $this->fileModel?->delete();
    }

    public function toArray(): array
    {
        return ['file' => $this->fileModel?->id, 'active' => $this->active, 'title' => $this->title];
    }

    public function jsonSerialize()
    {
        return json_encode($this->toArray());
    }

    public function __toString()
    {
        return $this->getUrl();
    }
}
