<?php

declare(strict_types=1);

return [
    'card' => [
        'recommendListTitle' => 'Похожие помещения'
    ],
    'components' => [
        'premisesCard' => [
            'callbackButton' => [
                'text' => 'Заказать звонок'
            ],
            'back' => 'Назад',
            'state' => [
                'booked' => 'Помещение забронировано'
            ],
            'properties' => [
                'title' => 'Характеристики квартиры',
                'titleNonResidential' => 'Характеристики помещения',
                'price' => ':value ₽',
                'area' => ':value м²',
                'areaTotal' => 'Общая площадь',
                'areaLiving' => 'Жилая',
                'areaKitchen' => 'Кухня',
            ],
            'building' => 'Корпус',
            'section' => 'Секция',
            'floor' => 'Этаж',
            'maxFloor' => 'из :value',
            'completion' => 'Срок сдачи',
            'additionalProperties' => 'Ещё характеристики',
            'features' => 'Особенности',
            'share' => [
                'title' => 'Поделиться',
                'saveToPdf' => 'Сохранить в PDF',
                'printToPdf' => 'Распечатать PDF',
                'sendByEmail' => 'Отправить по эл. почте',
                'email' => 'E-mail',
                'emailLong' => 'Электронная почта',
                'emailRequired' => 'Укажите e-mail адрес',
                'send' => 'Отправить',
                'sent' => 'Отправлено',
                'sending' => 'Отправляем',
                'sendToEmail' => 'Отправить на email',
                'resend' => 'Повторить отправку',
                'error' => 'Ошибка',
                'tryLater' => 'Попробуйте заполнить форму позднее',
                'pdfSentToEmail' => 'PDF-файл с квартирой отправлен вам на эл. почту'
            ],
            'plan' => [
                'openInFullScreen' => 'Открыть на весь экран',
                'planoplan' => '3D-тур',
                'plan' => 'Планировка',
                'planFurniture' => 'Планировка с мебелью',
                'plan3D' => 'Планировка 3D',
                'gallery' => 'Галерея',
                'onFloor' => 'На этаже',
                'onBuildingPlan' => 'На генплане'
            ],
            'pdf' => [
                'state' => [
                    'booked' => 'Помещение забронировано'
                ],
                'building' => 'Корпус',
                'section' => 'Секция',
                'floor' => 'Этаж',
                'maxFloor' => 'из :value',
                'completion' => 'Срок сдачи',
                'features' => 'Особенности',
                'properties' => [
                    'title' => 'Характеристики квартиры',
                    'price' => ':value ₽',
                    'area' => ':value м²',
                    'areaTotal' => 'Общая площадь',
                    'areaLiving' => 'Жилая',
                    'areaKitchen' => 'Кухня',
                ],
                'plan' => [
                    'plan' => 'Планировка',
                    'plan3D' => 'Планировка 3D',
                    'gallery' => 'Галерея',
                    'onFloor' => 'На плане этажа',
                    'onBuildingPlan' => 'На генплане'
                ],
                'planoplan' => [
                    'title' => 'Установите Planoplan <br>Real Estate!',
                    'text' => 'Сканируйте в приложении. Смотрите планировку в виртуальной реальности'
                ],
                'site' => 'Сайт',
                'phone' => 'Телефон',
                'schedule' => 'Время работы',
                'created' => 'Сгенерированно',
                'noOffer' => 'Не является офертой'
            ],
            'vr' => [
                'buttonText' => 'Посмотреть квартиру в VR'
            ],
            'mortgage' => [
                'title' => 'В ипотеку:',
                'from' => 'от',
                'currency' => '₽/мес.',
                'buttonText' => 'Рассчитать'
            ]
        ],
        'statList' => [
            'areaMin' => 'от :val м²',
            'priceMin' => 'от :val млн ₽'
        ],
        'recommendList' => [
            'title' => 'Похожие квартиры',
            'properties' => [
                'title' => 'Характеристики квартиры',
                'price' => ':value ₽',
                'area' => ':value м²',
                'areaTotal' => 'Общая площадь',
                'areaLiving' => 'Жилая',
                'areaKitchen' => 'Кухня',
            ],
            'building' => 'Корпус',
            'section' => 'Секция',
            'floor' => 'Этаж',
            'maxFloor' => 'из :value',
            'plan' => [
                'openInFullScreen' => 'Открыть на весь экран',
                'plan' => 'Планировка',
                'plan3D' => 'Планировка 3D',
                'gallery' => 'Галерея',
                'onFloor' => 'На этаже',
                'onBuildingPlan' => 'На генплане'
            ]
        ]
    ],
    'premisesTypeTitle' => [
        'flat' => 'квартира',
        'simple' => ':title № :number',
        'short' => ':title № :number',
        'shortInternal' => ':title № :number, :area м²',
        'shortWithRooms' => ':rooms-комнатная № :number',
        'shortWithRoomsInternal' => ':rooms-комнатная № :number, :area м²',
        'nonResidential' => ':title № :number',
        'nonResidentialInternal' => ':title № :number, :area м²'
    ]
];
