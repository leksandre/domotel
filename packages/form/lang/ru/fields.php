<?php

declare(strict_types=1);

return [
    'text' => [
        'title' => 'Текстовое короткое поле',
        'requiredMsg' => 'Заполните поле'
    ],
    'email' => [
        'title' => 'E-mail',
        'requiredMsg' => 'Укажите e-mail адрес'
    ],
    'phone' => [
        'title' => 'Телефон',
        'maskMsg' => 'Номер телефона должен состоять из 10 цифр',
        'requiredMsg' => 'Укажите номер телефона'
    ],
    'textarea' => [
        'title' => 'Текстовое длинное поле',
        'requiredMsg' => 'Заполните поле',
        'maxMsg' => 'Максимальное количество - $ символов'
    ],
    'additional' => [
        'title' => 'Дополнительные данные (техническое)'
    ],
];
