<?php

return [
    'route' => [
        'name' => 'kelnik.image',
        'domain' => null,
        'prefix' => 'storage/image'
    ],
    'disk' => 'public',
    'path' => 'image',

    'cache' => [
        'lifetime' => 864000 // Reset by schedule job
    ],

    /**
     * Image
     */
    'driver' => 'imagick', // gd, imagick, vips
    'blurAmount' => 50,
    'quality' => [
        'image/jpeg' => 90,
        'image/webp' => 82
    ],

    /**
     * Picture
     * 'min-width of viewport' => 'image width'
     */
    'breakpoints' => [
        2561 => 3840,
        1921 => 2560,
        1281 => 1920,
        769 => 1280,
        320 => 768
    ],
    'maxWidth' => 5000, // px
    'maxHeight' => 5000, // px
    'pixelRatio' => [1, 1.5, 2], // 1.0 ratio required

    /**
     * Available image formats
     */
    'formats' => ['jpeg', 'jpg', 'png', 'gif', 'webp', 'avif'],

    /**
     * Additional formats for `source` tag
     */
    'additionalFormats' => [
        //'avif' => 'image/avif'
        'webp' => 'image/webp',
    ],

    /**
     * Replace original image format to additional for `<source>` tag.
     * For example original image format is JPEG (jpg). To optimize the number of `<picture>` child nodes,
     * `<source>` node with link to `jpeg` are replaced with `<source>` with link to `webp`.
     * New format required in `additionalFormats`
     */
    'replaceFormats' => [
        'jpeg' => 'webp',
        'jpg' => 'webp',
        'gif' => 'webp',
        'png' => 'webp'
    ],

    /**
     * Lazy load
     */
    'lazyLoad' => false, // Automatically add for all `<picture>`
    'lazyLoadBackgroundWidth' => 100, // px

    /**
     * Show source or optimized path of image
     * If set `true` then return like `/storage/123/f00/image123.jpg`
     * If set `false` then return like `/storage/image/image123.jpg`
     */
    'allowOriginPath' => false
];
