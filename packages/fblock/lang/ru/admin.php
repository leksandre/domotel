<?php

declare(strict_types=1);

return [
    'moduleName' => 'Блок квартиры',
    'menu' => [
        'title' => 'Блок квартиры',
        'header' => '',
        'elements' => 'Список элементов'
    ],
    'tab' => [
        'base' => 'Основное',
        'preview' => 'Анонс',
        'content' => 'Содержимое'
    ],
    'filter' => [
        'title' => 'Поиск',
        'fieldTitle' => 'Заголовок'
    ],
    'add' => 'Добавить',
    'permission' => 'Управление блоком с квартирами',
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
    'back' => 'К списку записей',
    'save' => 'Сохранить',
    'saved' => 'Изменения сохранены',
    'newEntry' => 'Новая запись',
    'error' => 'Ошибка',
    'images' => 'Галерея изображений',
    'imagesHelp' => 'Не более 20 изображений',
    'elementsCount' => 'Записей',
    'ignorePageSlug' => 'Исключить алиас страницы из URL',
    'modelToPage' => 'Страница публикации категории',
    'modelToPageHelp' => 'На какую страницу выводить публикации данной категории',
    'area' => 'Площадь',
    'floor' => 'Этаж',
    'price' => 'Стоимость',
    'link' => 'Ссылка',
    'planoplan_code' => 'Код Planoplan',
    'features' => 'Особенности',
    'blocks' => 'Редактировать блоки в динамическом контенте',
    'button' => [
        'title' => 'Кнопка "Заказать звонок"',
        'text' => 'Текст кнопки',
        'defaultText' => 'Заказать звонок',
        'form' => 'Форма',
        'noValue' => '- Не выбрано -'
    ],
    'page' => [
        'module_page_not_exists' => 'Модуль страниц не найден, страница компонента не сохранена',
        'card_class_not_exists' => 'Компонент карточки записи не найден',
        'page_created' => 'Страница компонента сохранена',
        'page_create_or_update_error' => 'Ошибка при сохранении страницы компонента'
    ],
    'component' => [
        'headers' => [
            'content' => 'Контент',
            'settings'  => 'Настройки'
        ]
    ],
    'components' => [
        'blockList' => [
            'tabs' => [
                'content' => 'Основное',
                'settings' => 'Настройки'
            ],
            'title' => 'Блок Квартира',
            'titleField' => 'Заголовок',
            'titlePlaceholder' => 'Квартиры',
            'text' => 'Текст',
            'textHelp' => 'Не более 1000 символов',
            'image' => 'Изображение',
            'linkTitle' => 'Список элементов',
            'alias' => 'Алиас',
            'template' => 'Шаблон',
            'templates' => [
                'slider' => 'Слайдер',
                'cards1' => 'Карточки: 2 шт.',
                'cards2' => 'Карточки: 3 шт.'
            ]
        ]
    ]
];
