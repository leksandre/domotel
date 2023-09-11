<?php

declare(strict_types=1);

namespace Kelnik\Core\Helpers;

use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Orchid\Attachment\Models\Attachment;

final class ImageHelper
{
    public static function getImageSizes(Attachment $attachment): array
    {
        if (!$attachment->mime || !str_starts_with($attachment->mime, 'image')) {
            throw new InvalidArgumentException('Image required');
        }

        if ($attachment->getMimeType() === 'image/svg+xml') {
            return ImageHelper::getSvgSizes($attachment);
        }

        $imgData = Storage::disk($attachment->disk)->get($attachment->physicalPath());

        return getimagesize('data://' . $attachment->mime . ';base64,' . base64_encode($imgData));
    }

    public static function getSvgSizes(string|Attachment $svg): array
    {
        if ($svg instanceof Attachment) {
            $fp = Storage::disk($svg->disk)->readStream($svg->physicalPath());
            $svg = fread($fp, 1024);
            fclose($fp);
        }

        /**
         * Добавлены пробелы в поиск тега, чтобы игнорировать теги от inkscape
         * inkscape:window-height="..."
         * inkscape:window-width="..."
         */
        $width = (int) self::getAttributeValue($svg, ' width');
        $height = (int) self::getAttributeValue($svg, ' height');

        if ($width && $height) {
            return [$width, $height];
        }

        $viewBox = self::getAttributeValue($svg, 'viewBox');

        if ($viewBox) {
            [ , , $width, $height] = explode(' ', $viewBox);
            $width = (int)$width;
            $height = (int)$height;

            unset($svgPart, $startPos, $endPos);
        }
        unset($viewBox);

        return [$width, $height];
    }

    private static function getAttributeValue(string $node, string $attrName): bool|string
    {
        $attrName .= '="';
        $startPos = stripos($node, $attrName);

        if (!$startPos) {
            return false;
        }

        $nodePart = substr($node, $startPos + strlen($attrName), strlen($node));
        $endPos = stripos($nodePart, '"');

        return substr($nodePart, 0, $endPos);
    }
}
