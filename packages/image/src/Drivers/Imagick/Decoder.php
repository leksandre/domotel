<?php

namespace Kelnik\Image\Drivers\Imagick;

use Exception;
use GuzzleHttp\Psr7\Stream;
use Imagick;
use ImagickException;
use ImagickPixel;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Exception\NotSupportedException;
use Intervention\Image\Image;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class Decoder
{
    private mixed $data;

    public function __construct(mixed $data = null)
    {
        $this->data = $data;
    }

    public function initFromPath($path): Image
    {
        $core = new Imagick();

        try {
            $core->setBackgroundColor(new ImagickPixel('transparent'));
            $core->readImage($path);
            $core->setImageType(
                defined('\Imagick::IMGTYPE_TRUECOLORALPHA')
                    ? Imagick::IMGTYPE_TRUECOLORALPHA
                    : Imagick::IMGTYPE_TRUECOLORMATTE
            );
        } catch (ImagickException $e) {
            throw new NotReadableException('Unable to read image from path ({$path}).', 0, $e);
        }

        // build image
        $image = $this->initFromImagick($core);
        $image->setFileInfoFromPath($path);

        return $image;
    }

    public function initFromGdResource($resource): Image
    {
        throw new NotSupportedException('Imagick driver is unable to init from GD resource.');
    }

    public function initFromImagick(Imagick $object): Image
    {
        $object = $this->removeAnimation($object);
        $object->setImageOrientation(Imagick::ORIENTATION_UNDEFINED);

        return new Image(new Driver(), $object);
    }

    public function initFromBinary($binary): Image
    {
        $core = new Imagick();

        try {
            $core->setBackgroundColor(new ImagickPixel('transparent'));
            $core->readImageBlob($binary);
        } catch (ImagickException $e) {
            throw new NotReadableException('Unable to read image from binary data.', 0, $e);
        }

        // build image
        $image = $this->initFromImagick($core);
        $image->mime = $this->getMimeTypeFromBinary($binary);

        return $image;
    }

    private function removeAnimation(Imagick $object): Imagick
    {
        $imagick = new Imagick();

        foreach ($object as $frame) {
            $imagick->addImage($frame->getImage());
            break;
        }

        $object->destroy();

        return $imagick;
    }

    public function initFromUrl($url): Image
    {
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "Accept-language: en\r\n" .
                    "User-Agent: Mozilla/5.0 (Windows NT 6.1) " .
                                "AppleWebKit/537.2 (KHTML, like Gecko) " .
                                "Chrome/22.0.1216.0 Safari/537.2\r\n"
            ]
        ];

        $context = stream_context_create($options);


        if ($data = file_get_contents($url, false, $context)) {
            return $this->initFromBinary($data);
        }

        throw new NotReadableException('Unable to init from given url (' . $url . ').');
    }

    public function initFromStream($stream): Image
    {
        if (!$stream instanceof StreamInterface) {
            $stream = new Stream($stream);
        }

        try {
            $offset = $stream->tell();
        } catch (RuntimeException $e) {
            $offset = 0;
        }

        $shouldAndCanSeek = $offset !== 0 && $stream->isSeekable();

        if ($shouldAndCanSeek) {
            $stream->rewind();
        }

        try {
            $data = $stream->getContents();
        } catch (RuntimeException $e) {
            $data = null;
        }

        if ($shouldAndCanSeek) {
            $stream->seek($offset);
        }

        if ($data) {
            return $this->initFromBinary($data);
        }

        throw new NotReadableException('Unable to init from given stream');
    }

    public function isGdResource(): bool
    {
        if (is_resource($this->data)) {
            return get_resource_type($this->data) === 'gd';
        }

        return false;
    }

    public function isImagick(): bool
    {
        return is_a($this->data, Imagick::class);
    }

    public function isInterventionImage(): bool
    {
        return is_a($this->data, Image::class);
    }

    public function isSplFileInfo(): bool
    {
        return is_a($this->data, SplFileInfo::class);
    }

    public function isSymfonyUpload(): bool
    {
        return is_a($this->data, UploadedFile::class);
    }

    public function isFilePath(): bool
    {
        if (is_string($this->data)) {
            try {
                return is_file($this->data);
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    public function isUrl(): bool
    {
        return (bool)filter_var($this->data, FILTER_VALIDATE_URL);
    }

    public function isStream(): bool
    {
        return $this->data instanceof StreamInterface
            || is_resource($this->data)
            || get_resource_type($this->data) === 'stream';
    }

    public function isBinary(): bool
    {
        if ($this->data) {
            $mime = $this->getMimeTypeFromBinary($this->data);

            return !str_starts_with($mime, 'text') && $mime != 'application/x-empty';
        }

        return false;
    }

    public function isDataUrl(): bool
    {
        return is_null($this->decodeDataUrl($this->data));
    }

    public function isBase64(): bool
    {
        if (!is_string($this->data)) {
            return false;
        }

        return base64_encode(base64_decode($this->data)) === str_replace(["\n", "\r"], '', $this->data);
    }

    public function initFromInterventionImage($object): Image
    {
        return $object;
    }

    private function decodeDataUrl($dataUrl): ?string
    {
        if (!is_string($dataUrl)) {
            return null;
        }

        $pattern = '/^data:(?:image\/[a-zA-Z\-\.]+)(?:charset=\".+\")?;base64,(?P<data>.+)$/';
        preg_match($pattern, $dataUrl, $matches);

        if (is_array($matches) && array_key_exists('data', $matches)) {
            return base64_decode($matches['data']);
        }

        return null;
    }

    public function init($data): Image
    {
        $this->data = $data;

        return match (true) {
            $this->isGdResource() => $this->initFromGdResource($this->data),
            $this->isImagick() => $this->initFromImagick($this->data),
            $this->isInterventionImage() => $this->initFromInterventionImage($this->data),
            $this->isSplFileInfo() => $this->initFromPath($this->data->getRealPath()),
            $this->isBinary() => $this->initFromBinary($this->data),
            $this->isUrl() => $this->initFromUrl($this->data),
            $this->isStream() => $this->initFromStream($this->data),
            $this->isDataUrl() => $this->initFromBinary($this->decodeDataUrl($this->data)),
            $this->isFilePath() => $this->initFromPath($this->data),
            $this->isBase64() => $this->initFromBinary(base64_decode($this->data)),
            default => throw new NotReadableException('Image source not readable'),
        };
    }

    public function __toString()
    {
        return (string)$this->data;
    }

    private function getMimeTypeFromBinary(string $binary): string
    {
        return (new \finfo(FILEINFO_MIME_TYPE))->buffer(substr($binary, 0, 1024));
    }
}
