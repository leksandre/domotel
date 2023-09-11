# Подмодуль "Визуальный выборщик квартирографии" (`estate-visual`)

## Возможности
Создание визуальных выборщиков помещений.

## События
* `SelectorEvent` - при любых операциях с моделью `Selector`

## Слушатели
* `ResetSelectorCache` - сбрасывает кеш компонента `Selector` и `SelectorFrame`, подписан на событие `SelectorEvent`

## Задачи
* `ClearingModuleData` - очищает базу данных модуля
* `RemoveSelectorCache` - сбрасывает кеш выборщика

## Компоненты
* Визуальный выборщик
* Визуальный выборщик iframe

## Кеширование
Весь кеш подмодуля сохраняется с тегом названия подмодуля `estate-visual` и базового модуля квартирографии `estate`.

## Выборщик

### Фильтры поиска
Возможные фильтры делятся на 3 типа:
* Чекбокс (множественные варианты выбора), контракт - `\Kelnik\EstateVisual\Models\Filters\Contracts\AbstractCheckboxFilter`
* Слайдер (диапазан значений), контракт - `\Kelnik\EstateVisual\Models\Filters\Contracts\AbstractSliderFilter`

Базовым контрактом для всех типов является `\Kelnik\EstateVisual\Models\Filters\Contracts\AbstractFilter`

### Схема обработки

```text
+---------+     +--------------------+     +-------------+
| Request | --> | SelectorController | --> | StepHandler |
+---------+     +--------------------+     +-------------+
                    /   ^                        |
                   /     \                       |
+----------+      /       +--------------+       |
| Response | <---/        | StepResource | <------
+----------+              +--------------+
```

Данные отдаются в виде `json` через котроллер `\Kelnik\EstateVisual\Http\Controllers\SelectorController`.

Для преобразования данных в необходимый формат используются классы типа `JsonResource` из каталога `src/Http/Resources`.

