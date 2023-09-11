<?php

declare(strict_types=1);

return [
    'dateQuarter' => ':quarter кв. :year',
    'premisesStatusVariants' => [
        'forSale' => [
            'title' => 'Свободно',
            'card_available' => true
        ],
        'booked' => [
            'title' => 'Забронировано',
            'card_available' => true
        ],
        'sold' => [
            'title' => 'Продано',
            'card_available' => false
        ],
        'inactive' => [
            'title' => 'Неактивный',
            'card_available' => false
        ]
    ],
    'premisesTypeGroupVariants' => [
        'living' => 'Квартиры',
//        'apart' => 'Апартаменты',
        'commerce' => 'Коммерция',
        'parking' => 'Паркинги',
//        'pantry' => 'Кладовки'
    ],
    'premisesTypeGroupPlurals' => [
        'living' => ['квартиру', 'квартиры', 'квартир'],
        'commerce' => ['помещение', 'помещения', 'помещений'],
        'parking' => ['машиноместо', 'машиноместа', 'машиномест']
    ],
    'premisesTypeVariants' => [
        'living' => [
            'Студия',
            'Однокомнатная',
            'Двухкомнатная',
            'Трехкомнатная',
            'Четырехкомнатная',
            'Пятикомнатная'
        ],
        'apart' => [
            'Апартаменты'
        ],
        'commerce' => [
            'Коммерческое помещение'
        ],
        'parking' => [
            'Малое', 'Среднее', 'Большое'
        ],
        'pantry' => [
            'Кладовая'
        ]
    ],
    'premisesTypeVariantsShort' => [
        'living' => [
            'Ст',
            '1к',
            '2к',
            '3к',
            '4к',
            '5к'
        ],
        'commerce' => [
            'C'
        ],
        'parking' => [
            'S', 'M', 'L'
        ]
    ],
    'premisesFeatureGroupVariants' => [
        'general' => 'Общая группа',
    ],
    'premisesFeatureVariants' => [
        'general' => [
            'Большая гостиная',
            'Большая кухня',
            'Двусветовая квартира',
            'Гардеробная',
            'Евро планировка',
            'Окно в ванной',
            'Мастер-спальня',
            'Терраса',
            'Угловое остекление',
            'Окно на улицу',
            'Окна во двор',
            'Двухуровневая квартира',
            'Два санузла',
            'Малоэтажное строение'
        ]
    ]
];
