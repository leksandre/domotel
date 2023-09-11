<?php

use Kelnik\EstateVisual\Models\Filters\Type;

return [
    'assets' => [
        'css' => [
            'path' => public_path('assetsVisual/css'),
            'url' => '/assetsVisual/css'
        ],
        'js' => [
            'path' => public_path('assetsVisual/js'),
            'url' => '/assetsVisual/js'
        ]
    ],
    'filters' => [
        Type::class
    ],
];
