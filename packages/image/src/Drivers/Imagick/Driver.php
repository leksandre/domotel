<?php

namespace Kelnik\Image\Drivers\Imagick;

use Imagick;
use Intervention\Image\AbstractDriver;
use Intervention\Image\Exception\NotSupportedException;
use Intervention\Image\Image;
use Intervention\Image\Imagick\Color;

final class Driver extends AbstractDriver
{
    public function __construct(Decoder $decoder = null, Encoder $encoder = null)
    {
        if (!$this->coreAvailable()) {
            throw new NotSupportedException('ImageMagick module not available with this PHP installation.');
        }

        $this->decoder = $decoder ?? new Decoder();
        $this->encoder = $encoder ?? new Encoder();
    }

    public function newImage($width, $height, $background = null): Image
    {
        $background = new Color($background);

        $core = new Imagick();
        $core->newImage($width, $height, $background->getPixel(), 'png');
        $core->setType(Imagick::IMGTYPE_UNDEFINED);
        $core->setImageType(Imagick::IMGTYPE_UNDEFINED);
        $core->setColorspace(Imagick::COLORSPACE_UNDEFINED);

        return new Image(new self(), $core);
    }

    public function parseColor($value): Color
    {
        return new Color($value);
    }

    protected function coreAvailable(): bool
    {
        return extension_loaded('imagick') && class_exists('Imagick');
    }
}
