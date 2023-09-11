<?php

declare(strict_types=1);

namespace Kelnik\Page\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Page\Services\Contracts\PageService;
use Orchid\Attachment\Models\Attachment;

final class PageMeta implements Contracts\PageMeta
{
    private const TITLE = 'title';
    private const DESCRIPTION = 'description';
    private const KEYWORDS = 'keywords';
    private const IMAGE_ID = 'image_id';
    private const CACHE_TTL = 86400;

    private array $data = [];
    private int|string $pageId;
    private ?Attachment $image = null;
    private readonly PageService $pageService;
    private readonly AttachmentRepository $attachmentRepository;

    public function __construct(int|string $pageId, array $data = [])
    {
        $this->pageId = $pageId;
        $this->attachmentRepository = resolve(AttachmentRepository::class);
        $this->pageService = resolve(PageService::class);
        $this->fill($data ?? []);
    }

    public function fill(array $data): void
    {
        $data[self::IMAGE_ID] = (int)($data[self::IMAGE_ID] ?? 0);
        $this->data = Arr::only(
            $data,
            [self::TITLE, self::DESCRIPTION, self::KEYWORDS, self::IMAGE_ID]
        );
    }

    public function setTitle(?string $value): void
    {
        $this->data[self::TITLE] = $value;
    }

    public function getTitle(): ?string
    {
        return $this->data[self::TITLE] ?? null;
    }

    public function setDescription(?string $value): void
    {
        $this->data[self::DESCRIPTION] = $value;
    }

    public function getDescription(): ?string
    {
        return $this->data[self::DESCRIPTION] ?? null;
    }

    public function setKeywords(?string $value): void
    {
        $this->data[self::KEYWORDS] = $value;
    }

    public function getKeywords(): ?string
    {
        return $this->data[self::KEYWORDS] ?? null;
    }

    public function setImage(null|int|Attachment $value): void
    {
        if (is_int($value)) {
            $value = $this->attachmentRepository->findByPrimary($value);
        }

        $this->data[self::IMAGE_ID] = $value->getKey();
        $this->image = $value;
    }

    public function getImage(): ?Attachment
    {
        $this->loadImage();

        return $this->image;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    private function loadImage(): void
    {
        if (
            empty($this->data[self::IMAGE_ID])
            || ($this->image && $this->image->getKey() === $this->data[self::IMAGE_ID])
        ) {
            return;
        }

        $this->image = Cache::tags($this->pageService->getPageCacheTag($this->pageId))->remember(
            'pageMetaImage_' . $this->data[self::IMAGE_ID],
            self::CACHE_TTL,
            fn() => $this->attachmentRepository->findByPrimary($this->data[self::IMAGE_ID])
        );
    }

    public function deleteImage(): bool
    {
        $this->loadImage();

        return $this->image?->exists
            ? $this->attachmentRepository->delete($this->image)
            : false;
    }
}
