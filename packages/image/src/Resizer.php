<?php

declare(strict_types=1);

namespace Kelnik\Image;

use Exception;
use GuzzleHttp\Psr7\MimeType;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;
use Kelnik\Image\Contracts\Config;

class Resizer
{
    private Image $image;
    private string $format = 'jpg';
    private int $quality = 90;

    /**
     * Resizer constructor.
     *
     * @param mixed $src
     * @param Config $config
     *
     * @throws Exception
     */
    public function __construct(mixed $src, protected Config $config)
    {
        $driverName = $this->config->driver();
        $driver = sprintf('Kelnik\\Image\\Drivers\\%s\\Driver', ucfirst($driverName));

        if (!class_exists($driver)) {
            throw new Exception('Driver ' . $driverName . ' not found');
        }

        $driver = new $driver();
        $this->image = ImageManagerStatic::configure(['driver' => $driver])->make($src);
    }

    /**
     * @param Params $params
     *
     * @return static
     */
    public function setParams(Params $params): static
    {
        if ($params->crop && ($params->width || $params->height)) {
            $this->image->fit(
                $params->width,
                $params->height,
                static fn($constraint) => $constraint->upsize(),
                'top-left'
            );
        } elseif ($params->width || $params->height) {
            $this->image->resize(
                $params->width,
                $params->height,
                static fn($constraint) => $constraint->aspectRatio()
            );
        }

        if ($params->blur) {
            $this->image->blur($this->config->blurAmount());
        }

        $mimeType = MimeType::fromFilename($params->filename);
        $quality = $this->config->quality();
        $this->quality = $mimeType && isset($quality[$mimeType]) ? $quality[$mimeType] : $quality['image/jpeg'];

        $this->format = pathinfo($params->filename, PATHINFO_EXTENSION) ?? 'jpg';

        return $this;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function save(string $dst): bool
    {
        $this->image->save($dst, $this->quality);
        $this->image->destroy();

        return true;
    }

    public function getBlob(): string
    {
        $res = (string)$this->image->encode($this->format, $this->quality);
        $this->image->destroy();

        return $res;
    }
}
