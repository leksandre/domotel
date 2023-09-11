# Расположение

## Палитра компонента
* --color-white: #ffffff; // цвет фона секции, карты, кнопки фуллсайза, кнопок zoom
* --color-white-rgb: 255, 255, 255; // цвет
* --color-brand-text: #0b1739; // цвет текста подписей, цвет иконки кнопки фуллсайза
* --color-brand-text-rgb: 11, 23, 57; // основной цвет счётчика слайдов, цвет тени слайдера
* --color-brand-base: #95d0a1; // акцентный цвет счётчика слайдов, цвет фона кнопок навигации
* --color-brand-gray-rgb: 226, 226, 226; // цвет ховера кнопки фулсайза

## Настройки контейнера карты
* Главный контейнер - "j-location-map" имеет атрибуты "data-ajax" и "data-json" для входных данных
* data-json - принимает строку ( Base64 ), с данными карты
* data-ajax - урл, при отправке ajax на который приходят соответственно данные для карты

## Структура JSON
```json
{
   "data":{
      "zoom":"10",
      "zoomMargin":100,
      "center":[
         59.829133,
         30.54389
      ],
      "markers":[
         {
            "id":"1",
            "coords":[
               59.829133,
               30.53589
            ],
            "type":"cafe",
            "position":"top",
            "icon":{
               "type":"cafe",
               "src":"/webicons/legend/media/cafe.svg",
               "size":{
                  "width":"30",
                  "height":"30"
               }
            },
            "balloon":{
               "image":"/images/yandex-map/tooltip-image.jpeg",
               "title":"На чаёк к Марусе!",
               "text":"Маршрутки <br> к220, к268, к272, к275. к220б, к440а <br> Автобусы <br> 115, 115а, 189, 327, 328"
            }
         }
      ]
   }
}
```