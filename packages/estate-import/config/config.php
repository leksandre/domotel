<?php

declare(strict_types=1);

use Kelnik\EstateImport\Jobs\HistoryQueueProcessing;
use Kelnik\EstateImport\Jobs\AddDataFromSource;
use Kelnik\EstateImport\Jobs\RemoveOldHistory;

return [
    'storage' => [
        'config' => [
            'driver' => 'local',
            'root' => storage_path('app/estate-import'),
            'visibility' => 'public'
        ]
    ],
    'cache' => [
        'store' => 'estate-import'
    ],
    'logging' => [
        'config' => [
            'driver' => 'daily',
            'path' => storage_path('logs/estate-import/history.log'),
            'level' => env('APP_ENV') === 'production' ? 'info' : 'debug',
            'days' => 7
        ]
    ],
    'queue' => [
        'connection' => env('ESTATE_IMPORT_QUEUE_CONNECTION', config('queue.default')),
        'name' => env('ESTATE_IMPORT_QUEUE_NAME')
    ],
    'processTimeOut' => now()->diffInSeconds(now()->addHours(2)), // seconds
    'schedule' => [
        AddDataFromSource::class => '0 */6 * * *', // Add data from remote resource, every 6 h
        HistoryQueueProcessing::class => '*/2 * * * *', // Run history queue, every 2 min
        RemoveOldHistory::class => '0 4 * * *', // Remove old history rows, every day at 04:00
    ],
    'unique' => [
        'enable' => env('ESTATE_IMPORT_UNIQUE', true), // Check unique History hash
        'dateFrom' => now()->diffInSeconds(now()->addDays(3)) // seconds
    ],
    'source' => [
        \Kelnik\EstateImport\Sources\Allio\SourceType::class,
        \Kelnik\EstateImport\Sources\Csv\SourceType::class,
        \Kelnik\EstateImport\Sources\ProfitBase\SourceType::class,
        \Kelnik\EstateImport\Sources\Xml\SourceType::class
    ]
];
