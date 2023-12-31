# Модуль "Формы" (`form`)

## Возможности
* Создавать формы с произвольным набором полей
* Указывать название формы, описание, текст кнопки отправки формы, текст об успешной и неупешной отправке
* Отправлять уведомления о новыхзаявках формы

## События
* `FieldEvent` - событие при операциях с полем формы
* `FormEvent` - событие при операциях с формой
* `LogAddedEvent` - событие при добавлении заявки

## Слушатели
* `ResetFormCache` - подписан на `FormEvent` и `FieldEvent`, сбраасывает кеш компонента "Форма" по тегу
* `SendNotifyOnNewLog` - подписан на `LogAddedEvent`, отправляет уведомления, адресатам формы

## Blade компоненты
* Форма

## Особенности
* Обработка заявок формы происходит по URL `/api/form/submit/{id}`. Настроить маршрут можно в `routes/api.php`.
* Данные о заявке хранятся в БД в закодированном виде, при работе с заявками через панель администратора данные декодируются.

## Защита от спама
* Используется ограничение на количество отправки формы - не более 20 обращений в минуту
* Проверяется наличие CSRF токена
* В форму добавляется скрытое стилями поле, если оно заполнено, то заявка отклоняется. @see `Kelnik\Form\Services\FormService #48`
