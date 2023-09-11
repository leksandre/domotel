# Модуль "Квартирография" (`estate`)

## Возможности
* Управление списком ЖК, корпусов, секций, этаже, помещенй
* Управлением справочниками: типы помещений, статусы помещений, особенности помещений, сроки сдачи объектов

## События
* `EstateModelEvent`
* `PlanoplanEvent`

## Слушатели
* `DeletePageLink`
* `EstateModelModified`
* `PlanoplanCreated`
* `ResetPlanoplanCache`
* `ResetPremisesFeatureGroupCache`

## Задачи
* `CleanModuleCache` - очищает кеш модуля по тену `EstateServiceProvider::MODULE_NAME`
* `CleanModuleData` - очищает базу данных модуля
* `LoadPlanoplanData` - подгружает данные о виджете `Planoplan` после создания записи в БД.
* `StatUpdate` - обновляет статистику квартирографии
* `UpdatePlanoplanData` - обновляет данные о виджете `Planoplan`

## Компоненты
* `PremisesCard` - карточкапомещения (динамический)
* `RecommendList` - список рекомендованных помещений

## Blade-компоненты
* `StatList` - вывод статичного списка помещений

## Расписание задач
* Обновление данных о виджете `Planoplan`, по умолчанию запуск раз в 3 часа. Настраивается через конфиг.
