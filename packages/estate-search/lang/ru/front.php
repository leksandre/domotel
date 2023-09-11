<?php

declare(strict_types=1);

return [
    'form' => [
        'variants' => 'вариант|варианта|вариантов',
        'button' => 'Показать :count :variants',
        'area' => [
            'title' => 'Площадь общая, м²'
        ],
        'building' => [
            'title' => 'Корпус'
        ],
        'completion' => [
            'title' => 'Срок сдачи'
        ],
        'feature' => [
            'title' => 'Особенности'
        ],
        'floor' => [
            'title' => 'Этаж'
        ],
        'price' => [
            'title' => 'Стоимость, млн ₽'
        ],
        'type' => [
            'title' => 'Комнатность'
        ],
        'section' => [
            'title' => 'Секция'
        ],
        'state' => [
            'title' => 'Наличие',
            'all' => 'Все'
        ],
        'hideBooked' => [
            'title' => 'Скрыть забронированные'
        ],
        'priceSale' => [
            'title' => 'Квартиры со скидкой'
        ]
    ],
    'sort' => [
        'area' => [
            'asc' => 'Сначала с меньшей площадью',
            'desc' => 'Сначала с большей площадью'
        ],
        'price' => [
            'asc' => 'Сначала дешевле',
            'desc' => 'Сначала дороже'
        ],
        'rooms' => [
            'asc' => 'Сначала меньше комнат',
            'desc' => 'Сначала больше комнат'
        ]
    ],
    'results' => [
        'more' => 'Загрузить ещё :countMore :variants из :countLeft',
        'variants' => 'квартиру|квартиры|квартир',
    ],
    'booked' => 'Помещение забронировано',
    'hidePrice' => 'Цена по запросу',
    'pluralDefault' => 'помещение|помещения|помещений',
    'delivery' => 'Срок сдачи',
    'premises' => [
        'description' => [
            'floor' => 'Этаж',
            'section' => 'Секция',
            'building' => 'Корпус',
            'complex' => 'ЖК'
        ]
    ]
];
