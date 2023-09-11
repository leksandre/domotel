# Multi.Kelnik 2.0
Сайт жилого комплекса

## Как развернуть проект

### Запуск проекта через утилиту `make` и `Docker`

Данный вариант требует установки [make](https://ru.wikipedia.org/wiki/Make) и 
Docker ([Linux](https://gitbook.kelnik.pro/common/docker/ubuntu/), [MacOS](https://www.docker.com/get-started), [Windows](https://gitbook.kelnik.pro/common/docker/windows/)).

На Linux и MacOS утилита `make` уже установлена в системе.
Для `Windows` рекомендуется использовать [WSL](https://ru.wikipedia.org/wiki/Windows_Subsystem_for_Linux), 
либо [Cygwin](https://ru.wikipedia.org/wiki/Cygwin) и [mlink](https://ru.wikipedia.org/wiki/%D0%A1%D0%B8%D0%BC%D0%B2%D0%BE%D0%BB%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B0%D1%8F_%D1%81%D1%81%D1%8B%D0%BB%D0%BA%D0%B0).

Копируем ssh-ключ (`rsync.key`) в папку `storage/` для будущей синхронизации хранилища. 
Права на файл устанавливаем как `600`.
Для первого запуска проекта выполнить команду `make install`.
или `make install-backend` если не требуется установка зависимостей `npm`.
Если все зависимости проекта уже установлены, то запуск проекта можно выполнить командой `make up` или `make up-backend`.

#### Доступные команды
* `install` установить все зависимости `composer` и `npm`
* `install-backend` установка зависимостей только для `composer`
* `up` запустить проект (nginx+php, nodejs)
* `up-backend` запустить только `backend`
* `down` останавливает и удаляет все контейнеры проекта
* `clear-cache` полная очистка кэша `backend`
* `sync-packages` - актуализация пакетов composer и npm в соответствии с lock-файлами
* `sync-db` - импортирует базу данных с dev сервера в контейнер
* `sync-storage` - копирует файлы хранилища с dev сервера (требуется ssh-ключ)
* `sync` - запуск всех команд `sync-*`

### Запуск проекта через `Docker`

* Скопировать `.env.example` в `.env`
* Применить настройки окружения в `.env`
* Запустить сервис `web` и `db` - `docker compose up -d web db`, автоматически запустится сервис `php`
* В контейнере сервиса `php` выполнить команды: 
  * `composer install -n`
  * `php artisan storage:link --relative`
* Импортируем базу с dev сервера в контейнер
```bash
docker exec --env-file=".env" -it <APP_NAME>-db sh -c 'mysqldump -qQR --add-drop-table --skip-lock-tables --skip-comments --ssl -h${DB_SRC_HOST} -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} | mysql -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}'
```
* Копируем файлы из хранилища. Размещаем ключ `rsync.key` в папке `storage/`

```bash
docker exec --env-file=".env" -it <APP_NAME>-php sh -c 'rsync -azmh --exclude="image/" --delete -e "ssh -i ${RSYNC_KEY_PATH}" ${RSYNC_SRC_PATH} ${RSYNC_DST_PATH}'
```

### Запуск проекта обычным способом 

* Скопировать `.env.example` в `.env`
* Применить настройки окружения в `.env`
* Выполнить команды: 
  * `composer install -n`
  * `php artisan storage:link --relative`
* Меняем в `.env` значение параметра `DB_HOST` на `localhost` и импортируем базу с dev сервера в локальный сервер

```bash
export $(cat .env | xargs) && \
mysqldump -qQR --add-drop-table --skip-lock-tables --skip-comments --ssl -h${DB_SRC_HOST} -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} | \
mysql -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}
```

* Копируем файлы из хранилища. Для этого необходима утилита `rsync` и ssh-ключ `rsync.key`, который необходимо разместить в `storage/`

```bash
export $(cat .env | xargs) && \
LC_ALL="en_US.UTF-8" rsync -azm --exclude="image/" --delete -e "ssh -i ${RSYNC_KEY_PATH}" ${RSYNC_SRC_PATH} ${RSYNC_DST_PATH}
```

## Запуск проекта на площадке/сервере

1) Скопировать `.env.example` в `.env`
2) Настроить параметры окружения

* `APP_ENV` один из вариантов: development, stage, production
* `APP_DEBUG` для `production` указываем `false` в остальных случаях можно использовать `true`
* `APP_URL` указываем URL сайта, например https://some-host.ru
* `DB_*` - настройки подключения к БД
* `CACHE_DRIVER` - драйвер кеш, для `production` желательно memcached, redis. Варианты: apc, array, database, file, memcached, redis, dynamodb, octane, null
* `QUEUE_CONNECTION` - соединение для очередей задач,  для `production` желательно database, redis, sqs. Варианты: sync, database, beanstalkd, sqs, redis, null
* `MAIL_*` - настройки для отправки почты

3) Устанавливаем пакеты composer, генерируем ключ приложения и создаем ссылки для хранилища, создаем кеш
* `composer install -n`
* `php artisan key:generate --force`
* `php artisan storage:link --relative`
* `php artisan optimize`

4) Прописываем в крон скрипт расписания

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```
5) Запускаем воркер очередей, желательно прописать в supervisor

```bash
php artisan queue:work
```

## Раздел администрирования

Панель управления основана на пакете [`Orchid Platform`](https://github.com/orchidsoftware/platform)

## Тестирование

Для запуска тестов выполняем команду `composer test`

## Структура
* Модули - `packages/*`

***

## Сборка проекта: фронт
**NODE VERSION: 18**

* `npm run watch` - запускает browserSync, отслеживание изменений в twig-шаблонах, стилях
* `npm run prod` - запускает сборку для production'а

### Структура
**Сайт**
* **Исходники:**
    * **HTML-страницы**: `frontend/src/pages`
    * **Стили**: `frontend/src/common/styles.scss`
* **Компилируются в:**
    * **HTML-страницы**: `public/pages`
    * **Стили**: `public/css/common/styles.css`
