<?php

use Kelnik\Pdf\Drivers\Compressors\Config as CompressorConfig;
use Kelnik\Pdf\Drivers\Compressors\Factory as CompressorFactory;
use Kelnik\Pdf\Drivers\Generators\Config as GeneratorConfig;
use Kelnik\Pdf\Drivers\Generators\Factory as GeneratorFactory;

return [
    'connection' => env('PDF_GENERATOR', 'chrome-cli'),

    'connections' => [
        'chrome-dp' => [
            'driver' => GeneratorFactory::CDP,
            'url' => env('PDF_CHROME_CDP_URL')
        ],
        'chrome-cli' => [
            'driver' => GeneratorFactory::CHROME,
            'path' => env('PDF_CHROME_CLI_PATH', '/usr/local/bin/chromium-browser'),
            'hinting' => env('PDF_COMPRESS_HINTING', GeneratorConfig::HINTING_NONE),
        ],
        'wkhtml' => [
            'driver' => GeneratorFactory::WKHTML,
            'path' => env('PDF_WKHTML_CLI_PATH', '/usr/local/bin/wkhtmltopdf')
        ]
    ],
    'compress' => [
        'enable' => env('PDF_COMPRESS', false),
        'driver' => env('PDF_COMPRESS_DRIVER', CompressorFactory::GS),
        'path' => env('PDF_COMPRESS_PATH', '/usr/bin/gs'),
        'level' => env('PDF_COMPRESS_LEVEL', CompressorConfig::LEVEL_EBOOK),
    ],
    'storage' => [
        'config' => [
            'driver' => 'local',
            'root' => storage_path('app/pdf'),
            'visibility' => 'public'
        ],
        // Or use disk
        // 'disk' => config('filesystem.default', 'public'),
    ],
    'cache' => [
        'expired' => now()->diffInSeconds(now()->addDays(7)), // seconds
    ],
    'schedule' => [
        'cleaner' => '0 3 * * */3'
    ]
];
