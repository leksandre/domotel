<?php

declare(strict_types=1);

return [
    'moduleName' => 'Квартирография',
    'menu' => [
        'title' => 'Квартирография',
        'complexes' => 'Объекты',
        'buildings' => 'Корпуса',
        'sections' => 'Секции',
        'floors' => 'Этажи',
        'premises' => 'Помещения',
        'referenceBook' => 'Справочники',
        'completions' => 'Сроки сдачи',
        'premisesStatuses' => 'Статусы помещений',
        'premisesPlanTypes' => 'Типы планировок',
        'premisesTypes' => 'Типы помещений',
        'premisesFeatures' => 'Особенности помещений',
        'importPlan' => 'Импорт планировок',
        'services' => 'Сервисы'
    ],
    'tab' => [
        'base' => 'Основное',
        'content' => 'Содержимое',
        'prices' => 'Цены',
        'areas' => 'Площади',
        'features' => 'Параметры',
        'images' => 'Изображения',
        'planoplan' => 'Planoplan'
    ],
    'add' => 'Добавить',
    'permission' => 'Управление квартирографией',
    'id' => 'ID',
    'title' => 'Название',
    'shortTitle' => 'Короткое название',
    'url' => 'URL',
    'slug' => 'Алиас',
    'priority' => 'Сортировка',
    'active' => 'Активность',
    'action' => 'Участвует в акции',
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
    'estatePremises' => 'Редактировать записи в динамическом контенте',
    'address' => 'Адрес',
    'phone' => 'Телефон',
    'coords' => 'Координаты',
    'icon' => 'Иконка',
    'image' => 'Изображение',
    'description' => 'Описание',
    'external_id' => 'Внешний идентификатор',
    'floor_min' => 'Минимальный этаж',
    'floor_max' => 'Максимальный этаж',
    'noValue' => '- Не выбрано -',
    'ignorePageSlug' => 'Исключить алиас страницы из URL',
    'errors' => [
        'sectionHasAnotherBuilding' => 'Корпус секции и этажа должны совпадать',
        'request' => 'Ошибка запроса',
        'floors' => 'Не выбраны этажи',
        'files' => 'Не выбраны файлы'
    ],
    'filter' => [
        'fieldId' => 'ID',
        'fieldTitle' => 'Название',
        'fieldTitleOrExternalId' => 'Название или внешний идентификатор',
        'fieldComplex' => 'Комплекс',
        'fieldBuilding' => 'Корпус',
        'fieldFloor' => 'Этаж',
        'fieldSection' => 'Секция',
        'fieldType' => 'Тип',
        'fieldStatus' => 'Статус',
        'apply' => 'Применить',
        'reset' => 'Сбросить'
    ],
    'complex' => [
        'site_url' => 'URL отдельного сайта комплекса',
        'cover' => 'Изображение обложки',
        'logo' => 'Логотип',
        'description' => 'Описание комплекса',
        'options' => 'Особенности',
        'buildings' => 'Корпуса'
    ],
    'building' => [
        'complex' => 'Комплекс',
        'complex_plan' => 'Корпус на плане комплекса',
        'completion' => 'Дата сдачи',
        'floors' => 'Этажи',
        'sections' => 'Секции'
    ],
    'section' => [
        'building' => 'Корпус',
        'premises' => 'Помещения'
    ],
    'floor' => [
        'building' => 'Корпус',
        'number' => 'Номер',
        'premises' => 'Помещения'
    ],
    'premises' => [
        'title' => 'Помещение',
        'floor' => 'Этаж',
        'section' => 'Секция',
        'empty' => '- Без привязки -',
        'number' => 'Номер',
        'number_on_floor' => 'Номер на этаже',
        'rooms' => 'Количество комнат',
        'type' => 'Тип помещения',
        'originalType' => 'Тип помещения из выгрузки',
        'status' => 'Статус помещения',
        'originalStatus' => 'Статус помещения из выгрузки',
        'price' => 'Базовая цена',
        'priceTotal' => 'Конечная, с учетом скидок',
        'priceSale' => 'Цена по акции',
        'priceMeter' => 'Цена за метр кв.',
        'priceRent' => 'Стоимость аренды',
        'areaTotal' => 'Общая площадь',
        'areaLiving' => 'Жилая площадь',
        'areaKitchen' => 'Площадь кухни',
        'features' => 'Особенности',
        'imageList' => 'Планировка для списка',
        'imagePlan' => 'Планировка помещения',
        'imagePlanFurniture' => 'Планировка помещения с мебелью',
        'imagePlan2' => 'Планировка помещения 2',
        'image3D' => 'Планировка 3D',
        'imageOnFloor' => 'Расположение на этаже',
        'imageWindows' => 'Вид из окна',
        'gallery' => 'Галерея дополнительных изображений',
        'planoplanCode' => 'Код виджета',
        'additionalProps' => 'Доп. параметры',
        'propField' => [
            'key' => 'Ключ',
            'title' => 'Название',
            'value' => 'Значение',
        ],
        'planType' => 'Тип планировки',
        'vrLink' => 'Ссылка на VR'
    ],
    'completion' => [
        'eventDate' => 'Дата',
        'selectDate' => 'Укажите дату'
    ],
    'premisesStatus' => [
        'replace' => 'Переопределять при импорте',
        'empty' => '- Не переопределять -',
        'premisesCardAvailable' => 'Карточка помещения доступна',
        'hidePrice' => 'Скрывать цену',
        'takeStat' => 'Участвует в сборке статистики',
        'additionalText' => 'Дополнительный текст',
        'icon' => 'Иконка'
    ],
    'premisesPlanType' => [
        'complex' => 'Объект',
        'listImage' => 'Планировка для списка',
        'cardImage' => 'Планировка',
    ],
    'premisesType' => [
        'isLiving' => 'Жилые помещения',
        'buildTitle' => 'Формировать название помещения на основе количества комнат',
        'buildTitleHelp' => 'Применяется только для жилой недвижимости',
        'subTypes' => 'Подтипы',
        'rooms' => 'Комнат',
        'replace' => 'Переопределять',
        'empty' => '- Не переопределять -',
        'modelToPage' => 'Сайт ":site": Страница карточки помещения данной категории',
        'modelToPageHelp' => 'На какую страницу выводить карточку помещения данной категории',
        'plural' => [
            'title' => 'Названия во множественном числе',
            'placeholder' => ['квартиру', 'квартиры', 'квартир']
        ],
        'image' => 'Изображение по умолчанию для карточкаи помещения и результатов поиска',
        'imageShort' => 'Изображение'
    ],
    'premisesFeature' => [
        'active' => 'Отображать',
        'isGeneral' => 'Общая группа',
        'subFeatures' => 'Особенности',
    ],
    'import' => [
        'setComplex' => '- Выберите ЖК -',
        'setBuilding' => '- Выберите Корпус -',
        'setSection' => '- Выберите Секцию (не обязательно) -',
        'setFloor' => '- Выберите Этаж -',
        'button' => 'Импортировать',
        'type' => [
            'title' => 'В названии файлов указан',
            'numberOnFloor' => 'Номер на этаже',
            'number' => 'Номер помещения',
            'help' => '<br>1) Выбираем объект <br>' .
                '2) Выбираем корпус <br>' .
                '3) Выбираем этажи (на выбранных этажах скрипт будет искать номера помещений,' .
                'указанные в названиях файлов) <br>' .
                '4) Загружаем нужные изображения <br>' .
                '5) Нажимаем "Импортировать" <br><br>' .
                'Название файлов в формате <b>&lt;<strong>Номер</strong> помещения&gt;.(jpg|png|svg)</b>.<br>' .
                'Номера нескольких помещений можно указывать через запятую.<br>' .
                'Примеры: <ul>
                    <li>11.svg</li>
                    <li>11,22,33,44.svg</li>
                </ul>'
        ],
        'file' => [
            'plan' => 'Планировка',
            'searchPlan' => 'Планировка для результатов поиска',
            'floorPlan' => 'На плане этажа',
            'help' => 'Можно выбрать несколько файлов'
        ],
        'table' => [
            'type' => 'Тип изображения',
            'file' => 'Файл',
            'result' => 'Обновлено помещений'
        ],
        'resultTitle' => 'Результаты импорта',
        'result' => ':cnt',
        'errors' => [
            'fileRequired' => 'Необходимо загрузить файл',
            'fileUploadError' => 'Ошибка загрузки файла',
            'floorListRequired' => 'Не указан список этажей',
            'invalidImageField' => 'Некорректное название поля'
        ]
    ],
    'components' => [
        'premisesCard' => [
            'title' => 'Карточка помещения (динамический)',
            'data' => [
                'title' => 'Название',
                'recommendHeader' => 'Список других записей',
                'recommendTitle' => 'Заголовок',
                'recommendCount' => 'Количество записей',
            ],
            'settings' => [
                'title' => 'Основное'
            ],
            'route' => [
                'title' => 'URL',
                'prefix' => 'Префикс'
            ],
            'callback' => [
                'title' => 'Форма заявки'
            ],
            'vr' => [
                'title' => 'VR',
                'active' => 'Отображать кнопку',
                'buttonText' => 'Текст кнопки'
            ],
            'pdf' => [
                'title' => 'PDF',
                'phone' => 'Телефон',
                'phoneHelp' => 'Если не указан, то подставляется номер из глобальных настроек',
                'schedule' => 'График работы центрального офиса',
                'about' => [
                    'header' => 'О комплексе',
                    'title' => 'Заголовок',
                    'address' => 'Адрес объекта',
                    'images' => 'Изображения',
                    'text' => 'Текст',
                    'utp' => 'УТП',
                    'utpTitle' => 'Заголовок',
                    'utpText' => 'Текст'
                ]
            ],
            'template' => 'Шаблон',
            'templates' => [
                'residential' => 'Жилая',
                'non-residential' => 'Нежилая'
            ],
            'background' => 'Цвет фона',
            'backgrounds' => [
                'colorless' => 'Без цвета',
                'color' => 'Цветной'
            ],
            'meta' => [
                'title' => 'Meta-теги',
                'title_' => 'Шаблон формирования заголовка',
                'description' => 'Шаблон формирования описания',
                'keywords' => 'Шаблон формирования ключевых слов',
                'keywordsHelp' => 'разделитель - запятая'
            ],
            'replacement' => [
                'title' => 'Заменяемые слова',
                'src' => 'Переменная',
                'val' => 'Значение',
                'fields' => [
                    'price_total' => 'Итоговая стоимость',
                    'area_total' => 'Общая площадь',
                    'area_living' => 'Жилая площадь',
                    'rooms' => 'Количество комнат',
                    'title' => 'Название помещения',
                    'number' => 'Номер помещения',
                    'number_on_floor' => 'Номер на этаже',
                    'type' => [
                        'title' => 'Название типа помещения'
                    ],
                    'status' => [
                        'title' => 'Статус помещения'
                    ],
                    'floor' => [
                        'title' => 'Название этажа',
                        'number' => 'Номер этажа',
                        'building' => [
                            'title' => 'Название корпуса',
                            'complex' => [
                                'title' => 'Название комплекса'
                            ]
                        ]
                    ],
                    'section' => [
                        'title' => 'Название секции'
                    ],
                    'features' => 'Список особенностей, разделитель - запятая'
                ]
            ]
        ],
        'recommendList' => [
            'title' => 'Список рекомендованных помещений',
            'data' => [
                'title' => 'Название',
                'template' => 'Шаблон',
                'count' => 'Количество элементов'
            ],
            'templates' => [
                'residential' => 'Жилая'
            ]
        ]
    ]
];
