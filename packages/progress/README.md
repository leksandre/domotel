# Модуль "Хода строительства" (`progress`)

## Возможности
* Вывод фото и видео отчетов
* Вывод онлайн камер

## События
* `AlbumEvent` - при любых операциях с записями альбомов
* `AlbumVideoEvent` - при любых операциях с записями видео альбома
* `CameraEvent` - при любых операциях с записями онлайн-трансляций
* `GroupEvent` - при любых операциях с группами

## Слушатели
* `ResetAlbumCache` - очищает кеш по тегу альбома. Подписан на события `AlbumEvent` и `AlbumVideoEvent`
* `ResetCameraCache` - очищает кеш по тегу камеры. Подписан на событие `CameraEvent`
* `ResetGroupCache` - очищает кеш по тегу камеры. Подписан на событие `GroupEvent`

## Задачи
* `ClearingModuleData` - очищает базу данных модуля

## Компоненты страницы
* Ход строительства - Выводит весь список альбомов хода строительства и онлайн-камер без пагинации.

## Маршруты
* `kelnik.progress.cameras` (`GET|HEAD /api/progress/cameras`) - отдает список всех активных камер
* `kelnik.progress.albums` (`GET|HEAD /api/progress/albums`) - отдает список всех активных альбомов

Список всех `api` маршрутов находится в файле `routes/api.php`.