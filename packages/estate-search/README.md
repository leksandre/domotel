# Подмодуль "Параметрический поиск квартирографии" (`estate-search`)

## Возможности
* Настройка параметрического поиска
* Фильтрация помещений по параметрам

## События
нет

## Слушатели
нет

## Кеширование
Весь кеш подмодуля сохраняется с тегом названия подмодуля `estate-search` и базового модуля квартирографии `estate`.

## Сервис поиска
Для поиска используется сервис `\Kelnik\EstateSearch\Services\SearchService`.

При инициализации сервиса (метод `init`) добавляются необходимые фильтры и варианты сортировки.

### Фильтры поиска
Возможные фильтры делятся на 3 типа:
* Чекбокс (множественные варианты выбора), контракт - `\Kelnik\EstateSearch\Models\Filters\Contracts\AbstractCheckboxFilter`
* Слайдер (диапазан значений), контракт - `\Kelnik\EstateSearch\Models\Filters\Contracts\AbstractSliderFilter`
* Переключатель (вариант выбора да/нет), контракт - `\Kelnik\EstateSearch\Models\Filters\Contracts\AbstractToggleFilter`

Базовым контрактом для всех типов является `\Kelnik\EstateSearch\Models\Filters\Contracts\AbstractFilter`

### Сортировка результатов
Все варианты сортировки реализуют контракт `\Kelnik\EstateSearch\Models\Orders\Contracts\AbstractOrder`.

### Вывод результатов поиска
Данные для формы поиска и сами результаты отдаются в виде `json` через котроллер `\Kelnik\EstateSearch\Http\Controllers\SearchController`.

Для преобразования данных в необходимый формат используются классы типа `JsonResource` из каталога `src/Http/Resources`.
