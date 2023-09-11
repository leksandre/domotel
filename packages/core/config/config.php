<?php

return [
    /* Module route name prefix */
    'routeNamePrefix' => [
        'platform' => 'kelnik.platform.',
        'regular' => 'kelnik.'
    ],

    'map' => [
        'service' => 'yandex',// Fallback service name
        'center' => [59.829133, 30.543890],
        'zoom' => 10,
        'services' => [
            'yandex' => [
                'map' => \Kelnik\Core\Map\Yandex\Map::class,
                'marker' => \Kelnik\Core\Map\Yandex\Marker::class
            ]
        ]
    ],

    'site' => [
        'settings' => [
            'seo' => [
                'robots' => "User-agent: *\nDisallow:\n"
            ]
        ]
    ],

    'theme' => [
        'fonts' => [
            'ext' => ['woff', 'woff2']
        ],

        /** @see /frontend/src/common/styles/colors-basic.scss */
        'colors' => [
            'brand' => [
                'brand-text',
                'brand-base',
                'brand-gray',
                'brand-light',
                'brand-dark',
                'brand-headers',
                'additional-1',
                'additional-2',
                'additional-3',
                'additional-4',
                'additional-5'
            ],
            'component' => [
                'violet',
                'indigo',
                'blue',
                'green',
                'yellow',
                'orange',
                'red',
                'black',
                'white',
                'gray',
                'dark'
            ]
        ],
        'brandPrefix' => '$basic-',
        'scssPath' => base_path('frontend/src/common/styles/colors-basic.scss')
    ]
];
