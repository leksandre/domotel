<?php

declare(strict_types=1);

use Kelnik\EstateSearch\Models\Filters\Area;
use Kelnik\EstateSearch\Models\Filters\Building;
use Kelnik\EstateSearch\Models\Filters\Completion;
use Kelnik\EstateSearch\Models\Filters\Features;
use Kelnik\EstateSearch\Models\Filters\Floor;
use Kelnik\EstateSearch\Models\Filters\Price;
use Kelnik\EstateSearch\Models\Filters\PriceSale;
use Kelnik\EstateSearch\Models\Filters\Section;
use Kelnik\EstateSearch\Models\Filters\Type;
use Kelnik\EstateSearch\Models\Orders\AreaTotal;
use Kelnik\EstateSearch\Models\Orders\PriceTotal;
use Kelnik\EstateSearch\Models\Orders\Rooms;

return [
    'assets' => [
        'css' => [
            'path' => public_path('parametric/css'),
            'url' => '/parametric/css'
        ],
        'js' => [
            'path' => public_path('parametric/js'),
            'url' => '/parametric/js'
        ]
    ],
    'filters' => [
        Area::class,
        Building::class,
        Completion::class,
        Features::class,
        Floor::class,
        Price::class,
        PriceSale::class,
        Section::class,
        Type::class
    ],
    'orders' => [
        AreaTotal::class,
        PriceTotal::class,
        Rooms::class
    ]
];
