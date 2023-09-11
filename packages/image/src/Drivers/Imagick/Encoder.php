<?php

namespace Kelnik\Image\Drivers\Imagick;

use Imagick;
use ImagickPixel;
use Intervention\Image\Exception\NotSupportedException;

final class Encoder extends \Intervention\Image\Imagick\Encoder
{
    protected function processJpeg()
    {
        $format = 'jpeg';

        $imagick = $this->image->getCore();
        $imagick->setFormat($format);
        $imagick->setImageFormat($format);
        $imagick->stripImage();
        $imagick->setOption('jpeg:dct-method', 'float');
        $imagick->setOption('jpeg:optimize-coding', 'true');
        $imagick->setInterlaceScheme(Imagick::INTERLACE_LINE);
        $imagick->setSamplingFactors(['2x2', '1x1', '1x1']);
        $imagick->setImageCompressionQuality($this->quality);

        return $imagick->getImagesBlob();
    }

    protected function processWebp()
    {
        if (!Imagick::queryFormats('WEBP')) {
            throw new NotSupportedException('Webp format is not supported by Imagick installation.');
        }

        $format = 'webp';

        $imagick = $this->image->getCore();
        $imagick->setFormat($format);
        $imagick->setImageFormat($format);
        $imagick->setImageBackgroundColor(new ImagickPixel('transparent'));
        $imagick->mergeImageLayers(Imagick::LAYERMETHOD_MERGE);
        $imagick->stripImage();
        $imagick->setOption('webp:sns-strength', 15);
        $imagick->setOption('webp:thread-level', 1);
        $imagick->setOption('webp:method', 5);
        $imagick->setOption('webp:emulate-jpeg-size', 'true');
        $imagick->setImageCompressionQuality($this->quality);

        return $imagick->getImagesBlob();
    }
}
