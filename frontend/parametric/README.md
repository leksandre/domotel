# Параметрический поиск

## Project setup
**NODE VERSION: 14**
```
npm install
```

### Compiles and hot-reloads for development
```
npm run dev
```

### Compiles and minifies for production
```
npm run prod
```


### Lints and fixes files
```
npm run lint
```

### Тестовые данные

1. Для того чтобы сменить тестовые данные (пагинацию) необходимо раскомментировать код в файле `public/tests/parametric/parametric.php` 

### Пагинация

Пагинация может отсутствовать или приходить в виде объекта с данными.
Объект пагинации содержит следующие поля:
* `type: 'both' | 'page' | 'next'` - вид пагинации (и "Показать ещё" и кнопки переключения по страницам/только кнопки переключения по страницам/только "Показать ещё")
* `page: Number` - номер текущей страницы в пагинации
* `pages: Number` - общее количество страниц пагинации
* `limit: Number` - максимальное количество карточек на каждой из страниц
* `count: Number` - общее число помещений

Пример: пагинация отсутствует

```json
{
    "pagination": false
}
```

Пример: пагинация с кнопкой "Показать ещё" и кнопками переключения по страницам

```json
{
    "pagination": {
        "type": "both",
        "page": 1,
        "pages": 6,
        "count": 11,
        "limit": 2
    }
}
```
