<?php

declare(strict_types=1);

return [
    'moduleName' => 'Ход строительства',
    'menu' => [
        'title' => 'Ход строительства',
        'albums' => 'Отчеты',
        'cameras' => 'Онлайн-трансляции',
        'groups' => 'Группы'
    ],
    'tab' => [
        'base' => 'Основное',
    ],
    'errors' => [
        'emptyList' => 'Передан пустой список'
    ],
    'success' => 'Изменения сохранены',
    'add' => 'Добавить',
    'permission' => 'Управление отчетами хода строительства',
    'id' => 'ID',
    'title' => 'Название',
    'albumTitle' => 'Название альбома',
    'images' => 'Фотографии',
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
    'description' => 'Описание',
    'descriptionHelp' => 'Не более 400 символов',
    'link' => 'Ссылка',
    'videos' => 'Видео',
    'videoList' => 'Ссылки на видео',
    'videoHelp' => 'В альбоме видео отображается перед фотографиями. Разрешены ссылки на сервисы YouTube и Vimeo. ' .
        '<br>Например: https://www.youtube.com/embed/dQw4w9WgXcQ или https://player.vimeo.com/video/148751763',
    'imagesCount' => 'Фотографий',
    'videosCount' => 'Видео',
    'publishDate' => 'Дата для отображения',
    'selectDate' => 'Выбрать дату...',
    'cover' => 'Обложка',
    'contentLink' => 'Редактировать записи в динамическом контенте',
    'comment' => 'Комментарий',
    'group' => 'Группа',
    'components' => [
        'progress' => [
            'tabs' => [
                'content' => 'Контент',
                'settings' => 'Настройки'
            ],
            'title' => 'Ход строительства',
            'deadlines' => [
                'title' => 'Сроки сдачи',
                'fieldTitle' => ' Очередь',
                'fieldText' => 'Срок сдачи',
                'help' => 'Запись сохраняется, если заполнено поле "Заголовок" или "Текст"'
            ],
            'buttonText' => 'Текст на кнопке',
            'cameras' => 'Онлайн-трансляции',
            'albums' => 'Альбомы',
            'titleField' => 'Заголовок',
            'titlePlaceholder' => 'Как купить',
            'alias' => 'Алиас',
            'text' => 'Текст',
            'textHelp' => 'Не более 400 символов',
            'group' => 'Группа',
            'groupTitle' => 'Ограничить вывод альбомов и камер группой'
        ]
    ]
];
