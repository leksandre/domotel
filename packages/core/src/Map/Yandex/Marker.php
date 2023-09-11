<?php

declare(strict_types=1);

namespace Kelnik\Core\Map\Yandex;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Kelnik\Core\Helpers\ImageHelper;
use Kelnik\Core\Map\Contracts\Balloon;
use Kelnik\Core\Map\Contracts\Coords;
use Kelnik\Core\Map\Contracts\Icon;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Params;
use Kelnik\Image\Picture;
use Orchid\Attachment\Models\Attachment;

final class Marker implements \Kelnik\Core\Map\Contracts\Marker
{
    private const DEFAULT_TYPE = 'object';
    private const DEFAULT_Z_INDEX = 1;

    private const BALLOON_IMAGE_WIDTH = 560;
    private const BALLOON_IMAGE_HEIGHT = 320;

    private int $id = 0;
    private ?string $title;
    private ?string $type;
    private null|string|Icon $icon;
    private ?Balloon $balloon;
    private Coords $coords;
    private ?string $modifyClass;
    private readonly CoreService $coreService;

    /**
     * @param array $data
     *
     * @example $data = [
     *  'title' => string,
     *  'description' => string,
     *  'coords' => string|float[]|Coords
     *  'icon' => Attachment
     *  'image' => Attachment,
     *  'type' => string,
     *  'modifyClass' => string
     * ];
     */
    public function __construct(array $data)
    {
        $this->coreService = resolve(CoreService::class);

        if (is_string($data['coords'])) {
            $data['coords'] = explode(',', $data['coords']);
        }

        if (is_array($data['coords'])) {
            $data['coords'] = resolve(
                Coords::class,
                [
                    'lat' => (float)($data['coords'][0] ?? Coords::DEFAULT_COORDS),
                    'lng' => (float)($data['coords'][1] ?? Coords::DEFAULT_COORDS)
                ]
            );
        }

        $data['icon'] = isset($data['icon']) && ($data['icon'] instanceof Attachment || is_string($data['icon']))
                        ? $this->makeIcon($data['icon'], $data['type'] ?? self::DEFAULT_TYPE)
                        : null;

        $data['balloon'] = $this->makeBalloon($data);

        foreach (['coords', 'title', 'type', 'icon', 'balloon', 'modifyClass', 'code'] as $field) {
            $this->{$field} = $data[$field] ?? null;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type ?? '';
    }

    public function getTitle(): string
    {
        return $this->title ?? '';
    }

    public function getCoords(): Coords
    {
        return $this->coords;
    }

    public function getIcon(): null|string|Icon
    {
        return $this->icon ?? null;
    }

    public function getBalloon(): ?Balloon
    {
        return $this->balloon ?? null;
    }

    public function getModifyClass(): ?string
    {
        return $this->modifyClass;
    }

    public function toArray(): array
    {
        $res = [
            'id' => $this->id ?: rand(1, 100),
            'coords' => $this->coords?->toArray() ?? [Coords::DEFAULT_COORDS, Coords::DEFAULT_COORDS],
            'icon' => $this->icon?->toArray(),
            'type' => $this->type,
            'position' => 'top',
            'balloon' => $this->balloon?->toArray()
        ];

        if ($this->modifyClass !== null) {
            $res['modifyClass'] = $this->modifyClass;
        }

        if (!$res['type']) {
            $res['type'] = self::DEFAULT_TYPE;
            $res['not_cluster'] = true;
            $res['zIndex'] = self::DEFAULT_Z_INDEX;
            $res['modifyClass'] = 'object';
        }

        return $res;
    }

    private function makeIcon(string|Attachment $icon, string $type = null): ?Icon
    {
        if (is_string($icon)) {
            return mb_strlen($icon)
                ? new MarkerIcon(
                    $type ?? self::DEFAULT_TYPE,
                    $icon,
                    MarkerIcon::COMPLEX_WIDTH,
                    MarkerIcon::COMPLEX_HEIGHT
                )
                : null;
        }

        if (!$icon->exists) {
            return null;
        }

        $width = $height = 0;
        $storage = Storage::disk($icon->disk);

        if (!$storage->exists($icon->physicalPath())) {
            return new MarkerIcon($type ?? self::DEFAULT_TYPE, '', $width, $height);
        }

        $url = $icon->url;

        if ($icon->getMimeType() === 'image/svg+xml') {
            [$width, $height] = ImageHelper::getSvgSizes($icon);
        } elseif ($this->coreService->hasModule('image')) {
            $imageFile = new ImageFile($icon);
            $params = new Params($imageFile);
            $params->width = MarkerIcon::REAL_WIDTH;
            $params->height = MarkerIcon::REAL_HEIGHT;
            $width = MarkerIcon::WIDTH;
            $height = MarkerIcon::HEIGHT;

            if (!$type) {
                $params->width = MarkerIcon::COMPLEX_REAL_WIDTH;
                $params->height = MarkerIcon::COMPLEX_REAL_HEIGHT;
                $params->crop = true;
                $width = MarkerIcon::COMPLEX_WIDTH;
                $height = MarkerIcon::COMPLEX_HEIGHT;
            }

            $url = Picture::getResizedPath($imageFile, $params);
            unset($imageFile, $params);
        } else {
            $imgData = $storage->get($icon->physicalPath());
            $img = Image::make($imgData);
            $width = $img->getWidth();
            $height = $img->getHeight();
            $img->destroy();
        }
        unset($imgData);

        return new MarkerIcon($type ?? self::DEFAULT_TYPE, $url, $width, $height);
    }

    private function makeBalloon(array $data): ?Balloon
    {
        if (empty($data['title'])) {
            return null;
        }

        return resolve(
            Balloon::class,
            [
                'title' => $data['title'],
                'text' => $data['description'] ?? '',
                'imageUrl' => !empty($data['image']) && $data['image'] instanceof Attachment
                    ? $this->getImagePath($data['image'])
                    : (!empty($data['image']) && is_string($data['image']) ? $data['image'] : null)
            ]
        );
    }

    private function getImagePath(Attachment $image): ?string
    {
        if (!$image->exists) {
            return null;
        }

        if (!$this->coreService->hasModule('image') || strtolower($image->extension) === 'svg') {
            return $image->url;
        }

        $imageFile = new ImageFile($image);
        $params = new Params($imageFile);
        $params->width = self::BALLOON_IMAGE_WIDTH;
        $params->height = self::BALLOON_IMAGE_HEIGHT;

        return Picture::getResizedPath($imageFile, $params);
    }
}
