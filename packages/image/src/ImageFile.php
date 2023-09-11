<?php

declare(strict_types=1);

namespace Kelnik\Image;

use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Kelnik\Core\Helpers\ImageHelper;
use Orchid\Attachment\Models\Attachment;

final class ImageFile implements Contracts\ImageFile
{
    /** @var array<string, int> */
    protected array $sizes = [
        'width' => 0,
        'height' => 0
    ];

    public function __construct(private readonly Attachment $attachment)
    {
        if (!$this->isImage()) {
            throw new InvalidArgumentException('File is not image');
        }

        $storage = Storage::disk($this->attachment->disk);

        if (!$storage) {
            throw new InvalidArgumentException('Storage disk `' . $this->attachment->disk . '` not found');
        }

        if (!$storage->exists($this->attachment->physicalPath())) {
            return;
        }

        $sizes = ImageHelper::getImageSizes($this->attachment);

        if (!$sizes) {
            return;
        }

        $this->sizes = [
            'width' => $sizes[0] ?? 0,
            'height' => $sizes[1] ?? 0
        ];
    }

    protected function isImage(): bool
    {
        return in_array($this->getExtension(), config('kelnik-image.formats'));
    }

    public function getName(): string
    {
        return $this->attachment->name;
    }

    public function getFullName(): string
    {
        return $this->getName() . '.' . $this->getExtension();
    }

    public function getOriginalName(): string
    {
        return $this->attachment->original_name;
    }

    public function getPath(): string
    {
        $filePath = Storage::disk($this->attachment->disk)?->path('');

        if (!$filePath) {
            throw new InvalidArgumentException('Storage disk `' . $this->attachment->disk . '` not found');
        }

        return $filePath . ltrim($this->attachment->physicalPath(), DIRECTORY_SEPARATOR);
    }

    public function getUrl(): string
    {
        return $this->attachment->url;
    }

    public function getWidth(): int
    {
        return $this->sizes['width'];
    }

    public function getHeight(): int
    {
        return $this->sizes['height'];
    }

    public function getExtension(): string
    {
        return strtolower($this->attachment->extension);
    }

    public function getMimeType(): string
    {
        return $this->attachment->getMimeType();
    }

    public function getSize(): int
    {
        return $this->attachment->size;
    }
}
