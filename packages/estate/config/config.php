<?php

use Kelnik\Estate\Models\Planoplan;
use Kelnik\Estate\Models\Planoplan\WidgetV3;
use Kelnik\Estate\Models\Planoplan\WidgetV4;

return [
    'storage' => [
        'disk' => 'public'
    ],
    'card' => [
        'recommendCount' => 3
    ],
    'planoplan' => [
        'widget' => [
            'classes' => [
                Planoplan::VERSION_3 => WidgetV3::class,
                Planoplan::VERSION_4 => WidgetV4::class
            ],
            'plan' => Planoplan\Contracts\Widget::PLAN_3D
        ],
        'update' => [
            'schedule' => '0 */3 * * *',
            'edgeDate' => 7, // days
            'limit' => 0
        ]
    ]
];
