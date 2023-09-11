# Модуль "Контакты" (`contact`)

## Возможности
* Создание и вывод списка офисов
* Создание и вывод списка социальных сетей

## События
* `OfficeEvent` - при операциях с записями офисов
* `SocialLinkEvent` - при операциях с записями соц. сетей

## Слушатели
* `ResetOfficeCacheByTag` - сброс кеша списка офисов по событию `OfficeEvent`
* `ResetSocialCacheByTag` - сброс кеша списка соц. сетей по событию `SocialLinkEvent`

## Компоненты
* Offices

## Blade компоненты
* SocialLinks
