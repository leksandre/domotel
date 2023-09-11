<?php

declare(strict_types=1);

return [
    'moduleName' => 'Публикации',
    'menu' => [
        'title' => 'Публикации',
        'header' => 'Ленты новостей',
        'categories' => 'Категории',
        'elements' => 'Все публикации'
    ],
    'tab' => [
        'base' => 'Основное',
        'preview' => 'Анонс',
        'content' => 'Содержимое',
        'meta' => 'Meta-теги'
    ],
    'filter' => [
        'title' => 'Поиск',
        'fieldTitle' => 'Заголовок',
        'fieldCategory' => 'Категория'
    ],
    'add' => 'Добавить',
    'permission' => 'Управление публикациями',
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
    'previewImage' => 'Изображение для списка',
    'preview' => 'Текст анонса',
    'bodyImage' => 'Изображение для карточки',
    'images' => 'Галерея изображений',
    'imagesHelp' => 'Не более 20 изображений',
    'body' => 'Текст новости',
    'buttonText' => 'Текст кнопки',
    'buttonLink' => 'Ссылка с кнопки',
    'buttonTarget' => 'Открывать в новом окне',
    'category' => 'Категория',
    'activeDateRange' => 'Срок активности записи',
    'publishDateRange' => 'Срок действия для отображения',
    'dateStart' => 'Начало',
    'dateFinish' => 'Завершение',
    'publishDate' => 'Дата для отображения',
    'showTimer' => 'Показывать счетчик обратного отсчета',
    'button' => 'Показать кнопку',
    'selectDate' => 'Выбрать дату...',
    'elementsCount' => 'Записей',
    'ignorePageSlug' => 'Исключить алиас страницы из URL',
    'newsElements' => 'Редактировать записи в динамическом контенте',
    'modelToPage' => 'Сайт ":site": Страница публикации категории',
    'modelToPageHelp' => 'На какую страницу выводить публикации данной категории',
    'meta' => [
        'title' => 'Meta заголовок',
        'description' => 'Meta описание',
        'keywords' => 'Meta ключевые слова',
        'keywordsHelp' => 'разделитель запятая'
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
        'staticList' => [
            'title' => 'Список публикаций',
            'titleField' => 'Заголовок',
            'alias' => 'Алиас',
            'titlePlaceholder' => 'Новости',
            'categories' => 'Ограничить категориями',
            'categoriesHelp' => 'Если категории не указаны, то отображаются записи независимо от категории',
            'limit' => 'Количество элементов',
            'limitHelp' => 'Укажите количество выводимых элементов в диапазоне от :start до :finish',
            'page' => 'Страница карточки новости',
            'template' => 'Шаблон',
            'templates' => [
                'slider' => 'Слайдер',
                'mosaic' => 'Мозайка'
            ]
        ],
        'elementCard' => [
            'title' => 'Карточка публикации (динамический)',
            'data' => [
                'title' => 'Название',
                'otherHeader' => 'Список других записей',
                'otherTitle' => 'Заголовок',
                'otherCount' => 'Количество записей'
            ],
            'route' => [
                'title' => 'URL',
                'prefix' => 'Префикс'
            ],
            'template' => 'Шаблон',
            'templates' => [
                'news' => 'Новость',
                'action' => 'Акция'
            ]
        ]
    ]
];
