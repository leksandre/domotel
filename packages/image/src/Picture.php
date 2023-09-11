<?php

declare(strict_types=1);

namespace Kelnik\Image;

use Closure;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Image\Contracts\AbstractParams;
use Kelnik\Image\Contracts\Config;
use Kelnik\Image\Contracts\ImageFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * Generator of html tag `picture`
 * @link https://html.spec.whatwg.org/multipage/embedded-content.html#the-picture-element
 */
class Picture implements Contracts\Picture
{
    protected readonly ImageFile $image;

    protected array $breakpoints;
    protected array $attributes;
    protected readonly Config $config;
    protected int $lazyLoadBackgroundWidth = 0;
    protected bool $lazyLoad = false;
    protected bool $watermark = false;
    protected bool $replaceFormats = true;

    /**
     * min-width of viewport => image width
     */
    public const BREAKPOINTS = [
        2561 => 3840,
        1921 => 2560,
        1281 => 1920,
        769 => 1280,
        320 => 768
    ];

    /**
     * blurred image width of picture background
     */
    public const LAZY_LOAD_BACKGROUND_WIDTH = 50;

    public const ORIGINAL_RATIO = 1;

    /**
     * Picture constructor.
     *
     * @param ImageFile $imageFile Attachment id | Attachment full name | ImageFile object
     */
    public function __construct(ImageFile $imageFile, Config $config)
    {
        $this->image = $imageFile;
        $this->config = $config;
        $this->breakpoints = $this->config->breakpoints() ?: static::BREAKPOINTS;
        $this->lazyLoadBackgroundWidth = $this->config->lazyLoadBackgroundWidth()
                                            ?: static::LAZY_LOAD_BACKGROUND_WIDTH;
        $this->attributes = [
            'picture' => [],
            'source' => [],
            'image' => []
        ];
    }

    /** @throws FileNotFoundException */
    public static function init(int|string|ImageFile $imageFile): static
    {
        return new static(static::imageFileFactory($imageFile), static::configFactory());
    }

    /**
     * @param $imageFile
     *
     * @return ImageFile
     */
    protected static function imageFileFactory($imageFile): ImageFile
    {
        if ($imageFile instanceof ImageFile) {
            return $imageFile;
        }

        /** @var AttachmentRepository $attachmentRepo */
        $attachmentRepo = resolve(AttachmentRepository::class);

        if (is_int($imageFile)) {
            $imageFile = $attachmentRepo->findByPrimary($imageFile);
        } elseif (is_string($imageFile)) {
            $fileInfo = pathinfo($imageFile);
            $imageFile = $attachmentRepo->findByNameAndExtension($fileInfo['filename'], $fileInfo['extension']);
        }

        abort_if(!$imageFile || !$imageFile->exists, Response::HTTP_NOT_FOUND);

        return new \Kelnik\Image\ImageFile($imageFile);
    }

    protected static function configFactory(): Config
    {
        return new \Kelnik\Image\Config();
    }

    public function setLazyLoad(bool $value): static
    {
        $this->lazyLoad = $value;

        return $this;
    }

    public function useLazyLoad(): bool
    {
        return $this->lazyLoad || $this->config->useLazyLoad();
    }

    public function setLazyLoadBackgroundWidth(int $width): static
    {
        $this->lazyLoadBackgroundWidth = $width;

        return $this;
    }

    public function setBreakpoints(array $breakpoints): static
    {
        $this->breakpoints = $breakpoints;

        return $this;
    }

    public function setReplaceFormats(bool $value): static
    {
        $this->replaceFormats = $value;

        return $this;
    }

    protected function replaceFormats(): bool
    {
        return $this->replaceFormats && $this->config->replaceFormats();
    }

    protected function sourceForOriginFormatRequired(string $originExtension): bool
    {
        if (!$this->replaceFormats()) {
            return true;
        }

        $originExtension = strtolower($originExtension);
        $newFormat = $this->config->replaceFormats()[$originExtension] ?? '';

        return !$newFormat || !isset($this->config->additionalFormats()[$newFormat]);
    }

    public function setPictureAttribute(string $name, Closure|string $value): static
    {
        return $this->setAttribute('picture', $name, $value);
    }

    public function setImageAttribute(string $name, Closure|string $value): static
    {
        return $this->setAttribute('image', $name, $value);
    }

    public function setSourceAttribute(string $name, Closure|string $value): static
    {
        return $this->setAttribute('source', $name, $value);
    }

    protected function setAttribute(string $type, string $name, Closure|string $value): static
    {
        $this->attributes[$type][$name] = $value;

        return $this;
    }

//    public function setWatermark(bool $value): static
//    {
//        $this->watermark = $value;
//
//        return $this;
//    }

    public function render(): ?string
    {
        $useLazyLoad = $this->useLazyLoad();
        $defParams = Params::createFromArray([
            'filename' => $this->image->getFullName()
        ]);

        $pictureAttr = $this->attributes['picture'];
        if ($useLazyLoad) {
            $bgParams = clone $defParams;
            $bgParams->width = $this->lazyLoadBackgroundWidth;
            $bgParams->blur = true;
            $curStyle = $pictureAttr['style'] ?? '';

            $pictureAttr['style'] = $curStyle .
                ($curStyle ? ';' : '') .
                'background-image:url(\'' . static::getResizedPath($this->image, $bgParams, $this->config) . '\');';
            unset($bgParams);
        }

        $html = $this->renderNodeAttributes('<picture', $pictureAttr) . '>';
        unset($pictureAttr);

        $pixelRatio = $this->config->pixelRatio() ?? [static::ORIGINAL_RATIO];
        $convertAttributes = ['title', 'alt'];

        foreach ($this->attributes as &$nodeAttr) {
            if (!$nodeAttr) {
                continue;
            }

            foreach ($convertAttributes as $attrName) {
                if (isset($nodeAttr[$attrName])) {
                    $nodeAttr[$attrName] = e($nodeAttr[$attrName]);
                }
            }
        }
        unset($nodeAttr);

        $dataPrefix = '';
        if ($useLazyLoad) {
            $dataPrefix = 'data-';
            $this->attributes['source']['loading'] = $this->attributes['image']['loading'] = 'lazy';
        }

        krsort($this->breakpoints);

        $originRequired = $this->sourceForOriginFormatRequired($this->image->getExtension());

        foreach ($this->breakpoints as $breakPoint => $imageWidth) {
            if ($imageWidth > $this->image->getWidth() || $imageWidth > $this->config->maxWidth()) {
                continue;
            }

            $params = clone $defParams;
            $params->width = $imageWidth;

            $sourceAttr = $this->attributes['source'];
            $sourceAttr['media'] = $sourceAttr['media'] ?? '(min-width:' . $breakPoint . 'px)';
            $sourceAttr['type'] = $sourceAttr['type'] ?? $this->image->getMimeType();

            // Additional formats
            $tmpParams = clone $params;
            $tmpAttr = $sourceAttr;
            foreach ($this->config->additionalFormats() as $newExtension => $mimeType) {
                if ($mimeType === $this->image->getMimeType()) {
                    continue;
                }
                $tmpParams->filename = $this->image->getName() . '.' . $newExtension;
                $tmpAttr['type'] = $mimeType;
                $tmpAttr[$dataPrefix . 'srcset'] = $this->renderSourceRatio(
                    $this->image,
                    $pixelRatio,
                    $tmpParams
                );
                $html .= $this->renderNodeAttributes('<source', $tmpAttr) . '>';
            }
            unset($tmpParams, $tmpAttr);

            // Origin format
            if (!$originRequired) {
                continue;
            }

            $sourceAttr[$dataPrefix . 'srcset'] = $this->renderSourceRatio($this->image, $pixelRatio, $params);
            $html .= $this->renderNodeAttributes('<source', $sourceAttr) . '>';
        }
        unset($params, $dataPrefix, $sourceAttr);

        $imgAttr = $this->attributes['image'];
        $imgAttr['src'] = $imgAttr['src'] ??
                                ($this->config->useOriginalPath()
                                    ? $this->image->getUrl()
                                    : $this->getResizedPath($this->image, $defParams));

        if ($useLazyLoad) {
            $imgAttr['data-src'] = $imgAttr['src'];
            unset($imgAttr['src']);
        }

        $checking = [
            'decoding' => 'async',
            'alt' => '',
            'width' => $this->image->getWidth(),
            'height' => $this->image->getHeight()
        ];

        foreach ($checking as $attrName => $attrValue) {
            $imgAttr[$attrName] ??= $attrValue;
        }

        $html .= $this->renderNodeAttributes('<img', $imgAttr) . '>';
        unset($imgAttr);

        return $html . '</picture>';
    }

    protected function renderNodeAttributes(string $node, array $attributes): string
    {
        if (!$attributes) {
            return $node;
        }

        foreach ($attributes as $k => $v) {
            if (is_callable($v)) {
                $v = call_user_func($v, $this->image, $attributes);
            }
            $node .= ' ' . $k . '="' . $v . '"';
        }

        return $node;
    }

    /**
     * @param ImageFile $imageFile
     * @param float[] $ratios
     * @param AbstractParams $params
     *
     * @return string|null
     */
    protected function renderSourceRatio(ImageFile $imageFile, array $ratios, AbstractParams $params): ?string
    {
        $res = [];
        $addSuffix = count($ratios) > 1;
        foreach ($ratios as $ratio) {
            $tmpParams = clone $params;
            $tmpParams->width = (int)ceil($tmpParams->width * $ratio);
            if ($tmpParams->width > $imageFile->getWidth()) {
                continue;
            }
            $res[] = $this->getResizedPath($imageFile, $tmpParams) .
                ($addSuffix && $ratio !== static::ORIGINAL_RATIO ? ' ' . $ratio . 'x' : null);
        }

        return implode(', ', $res);
    }

    public static function getResizedPath(
        ImageFile $imageFile,
        AbstractParams $params,
        ?Config $config = null
    ): string {
        if (!$config) {
            $config = static::configFactory();
        }

        if (
            $params->width &&
            ($params->width >= $imageFile->getWidth() ||
                ($config->maxWidth() && $params->width >= $config->maxWidth())
            )
        ) {
            $params->width = null;
        }

        if (
            $params->height &&
            ($params->height >= $imageFile->getHeight() ||
                ($config->maxHeight() && $params->height >= $config->maxHeight())
            )
        ) {
            $params->height = null;
        }

        return route(
            config('kelnik-image.route.name'),
            [
                'width' => $params->width ? 'w' . $params->width . '/' : false,
                'height' => $params->height ? 'h' . $params->height . '/' : false,
                'crop' => $params->crop && ($params->width || $params->height) ? 'c/' : false,
                'blur' => $params->blur ? 'b/' : false,
                'filename' => $params->filename
            ],
            false
        );
    }
}
