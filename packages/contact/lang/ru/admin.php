<?php

declare(strict_types=1);

return [
    'moduleName' => 'Контакты',
    'menu' => [
        'title' => 'Контакты',
        'offices' => 'Офисы',
        'social' => 'Социальные сети'
    ],
    'tab' => [
        'base' => 'Основное',
        'preview' => 'Анонс',
        'content' => 'Содержимое'
    ],
    'filter' => [
        'title' => 'Поиск',
        'fieldTitle' => 'Заголовок',
        'fieldCategory' => 'Категория'
    ],
    'add' => 'Добавить',
    'permission' => 'Управление списком офисов и соц. сетей',
    'id' => 'ID',
    'title' => 'Название',
    'url' => 'URL',
    'slug' => 'Алиас',
    'priority' => 'Сортировка',
    'active' => 'Активность',
    'created' => 'Создано',
    'updated' => 'Изменено',
    'delete' => 'Удалить',
    'deleted' => 'Запись удалена',
    'deleteConfirm' => 'Удалить ":title"',
    'success' => 'Изменения сохранены',
    'back' => 'К списку записей',
    'save' => 'Сохранить',
    'saved' => 'Изменения сохранены',
    'newEntry' => 'Новая запись',
    'error' => 'Ошибка',
    'contactElements' => 'Редактировать записи в динамическом контенте',
    'schedule' => [
        'title' => 'Расписание',
        'day' => 'День',
        'time' => 'Время'
    ],
    'region' => 'Область',
    'city' => 'Населенный пункт',
    'street' => 'Улица и номер дома',
    'address' => 'Адрес',
    'email' => 'E-mail',
    'phone' => 'Телефон',
    'route_link' => 'Ссылка на маршрут',
    'coords' => 'Координаты',
    'icon' => 'Иконка',
    'link' => 'Ссылка',
    'image' => 'Изображение',
    'component' => [
        'headers' => [
            'content' => 'Контент',
            'settings'  => 'Настройки'
        ]
    ],
    'components' => [
        'offices' => [
            'title' => 'Контакты: офисы',
            'titleField' => 'Заголовок',
            'titleHelp' => 'Контакты',
            'alias' => 'Алиас',
            'titlePlaceholder' => 'Контакты',
            'zoom' => 'Зум',
            'zoomHelp' => 'Необходимо указать, если на карте 1 маркер. ' .
                'Если не заполнено,масштаб рассчитывается автоматически',
        ]
    ]
];
