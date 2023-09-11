<?php

declare(strict_types=1);

return [
    'moduleName' => 'Главный модуль',
    'permissions' => [
        'settings' => 'Настройки сайта',
        'sites' => 'Управление сайтами',
        'pref' => 'Инструменты разработчика'
    ],
    'system' => 'Система',
    'control' => 'Управление',
    'save' => 'Сохранить',
    'resetCache' => 'Сбросить кеш',
    'site' => [
        'menu' => 'Сайты',
        'back' => 'К списку сайтов',
        'add' => 'Добавить сайт',
        'id' => 'ID',
        'created' => 'Создан',
        'updated' => 'Изменен',
        'title' => 'Название',
        'active' => 'Активен',
        'primary' => [
            'title' => 'Основной',
            'help' => 'Только один сайт может иметь данный статус'
        ],
        'locale' => 'Язык',
        'type' => 'Тип',
        'types' => [
            'site' => 'Сайт',
            'touch' => 'Touch',
            'vr' => 'VR',
        ],
        'hosts' => 'Домены',
        'host' => 'Домен',
        'deleted' => 'Сайт удален',
        'delete' => 'Удалить',
        'saved' => 'Изменения сохранены',
        'deleteConfirm' => 'Удалить ":title"',
        'defaultTitle' => 'Сайт',
        'seo' => [
            'robots' => 'Содержимое robots.txt'
        ]
    ],
    'settings' => [
        'menu' => 'Настройки',
        'title' => 'Настройки сайта',
        'base' => [
            'title' => 'Основное',
            'complexName' => 'Название комплекса',
            'phone' => 'Телефон',
            'email' => 'E-mail',
            'emailReply' => 'E-mail, указанный как обратный адрес в уведомлениях',
            'logo' => [
                'title' => 'Логотип',
                'light' => 'Светлый логотип',
                'dark' => 'Темный логотип',
                'help' => 'Предпочтительно загружать изображения в формате SVG'
            ],
            'favicon' => [
                'title' => 'Fav-иконка',
                'help' => 'Картинка в формате <b>PNG</b>. Максимальное разрешение не более <b>:sizes px</b>'
            ],
            'animation' => [
                'active' => 'Активно',
                'title' => 'Анимация на сайте'
            ],
            'rounding' => [
                'active' => 'Активно',
                'title' => 'Скругление границ элементов'
            ]
        ],
        'colors' => [
            'title' => 'Цвета',
            'brand-text' => 'Фирменный цвет текста',
            'brand-base' => 'Фирменный цвет, базовый',
            'brand-gray' => 'Фирменный цвет, серый',
            'brand-light' => 'Фирменный цвет светлее',
            'brand-dark' => 'Фирменный цвет темнее',
            'brand-headers' => 'Фирменный цвет заголовков',
            'additional-1' => 'Дополнительный цвет 1',
            'additional-2' => 'Дополнительный цвет 2',
            'additional-3' => 'Дополнительный цвет 3',
            'additional-4' => 'Дополнительный цвет 4',
            'additional-5' => 'Дополнительный цвет 5',
            'violet' => 'Фиолетовый',
            'indigo' => 'Индиго',
            'blue' => 'Голубой',
            'green' => 'Зеленый',
            'yellow' => 'Желтый',
            'orange' => 'Оранжевый',
            'red' => 'Красный',
            'black' => 'Черный',
            'white' => 'Белый',
            'gray' => 'Серый',
            'dark' => 'Темный'
        ],
        'fonts' => [
            'title' => 'Шрифты',
            'regular' => 'Регулярный',
            'bold' => 'Жирный',
            'text' => 'Загрузите шрифт с компьютера. Вам потребуется два начертания (файла) шрифта — Regular и Bold. ' .
                'После загрузки отметьте чекбокс «Подключить», чтобы шрифт отобразился на сайте.',
            'subhead' => 'Основной шрифт текста',
            'active' => 'Подключить'
        ],
        'map' => [
            'title' => 'Карта',
            'yandex' => 'Яндекс',
            'service' => 'Сервис карт',
            'api' => [
                'title' => 'API ключ для карт на сайте',
                'search' => 'API ключ для поиска организаций',
                'searchHelp' => 'Максимальное число запросов на поиск по организациям составляет 500 запросов в сутки'
            ],
            'zoom' => 'Зум',
            'dragMode' => [
                'title' => 'Режим прокрутки карты на мобильных устройствах',
                'types' => [
                    'single' => 'Одним пальцем',
                    'double' => 'Двумя пальцами'
                ]
            ]
        ],
        'jsCodes' => [
            'title' => 'Внешние коды',
            'active' => 'Активность',
            'name' => 'Название',
            'section' => 'Секция',
            'code' => 'Код'
        ],
        'cache' => [
            'title' => 'Кэширование'
        ],
        'cookieNotice' => [
            'title' => 'Уведомление о Cookie',
            'active' => 'Отображать на сайте',
            'buttonText' => 'Текст кнопки',
            'text' => 'Текст блока',
            'popupText' => 'Текст popup',
            'linkText' => 'Текст ссылки',
            'link' => 'Ссылка',
            'linkHelp' => 'Если не указано, то открывается текст в popup',
            'expired' => 'Время действия соглашения (дней)'
        ]
    ],
    'menuDatabase' => 'Динамический контент',
    'about' => [
        'title' => 'О системе',
        'modules' => [
            'title' => 'Модули',
            'list' => [
                'title' => 'Название',
                'version' => 'Версия',
                'components' => 'Компоненты'
            ]
        ],
        'php' => 'Информация о PHP',
        'database' => 'Сервер БД',
        'mailer' => 'Сервис отправки почты',
        'env' => 'Название окружения',
        'debug' => [
            'title' => 'Режим отладки',
            'on' => '<span class=":color">Включен</span>',
            'off' => '<span class=":color">Выключен</span>'
        ],
        'queue' => 'Драйвер очередей'
    ],
    'tools' => [
        'title' => 'Инструменты',
        'clearing' => [
            'title' => 'Удаление данных модуля',
            'modules' => [
                'title' => 'Выберите модули для очистки',
                'button' => 'Выполнить очистку',
                'confirm' => 'Данные и файлы выбранных модулей будут удалены',
            ],
            'exec' => [
                'sync' => 'Модули очищены',
                'queue' => 'Задача поставлена в очередь, вы будете уведомлены о результатах'
            ],
            'notify' => [
                'title' => 'Очистка модулей завершена',
                'message' => 'Очищено модулей :success, с ошибкой :error.'
            ]
        ]
    ],
    'model_saved_success' => 'Изменения сохранены',
    'upload_file_from_your_pc' => 'Загрузите файл с вашего компьютера:',
    'margin' => [
        'top' => 'Отступ сверху',
        'bottom' => 'Отступ снизу',
        'title' => 'Вариант №:num, :px',
    ]
];