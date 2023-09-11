# Попап

## Попап галерей
1. Кнопка вызова попапа (размещается в слайдерах после тега picture/img) вида: 

~~~html
<button class="slider__fullscreen-button j-popup"
        data-gallery="true"
        data-src="/images/picture/first-screen-bg.jpg"
        data-alt="Изображение ЖК"
        data-caption="Изображение ЖК"
        data-slider="3"
        aria-label="Open in fullscreen">
    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M1 4C1.55228 4 2 4.44772 2 5V10H7C7.55228 10 8 10.4477 8 11C8 11.5523 7.55228 12 7 12H0V5C0 4.44772 0.447715 4 1 4Z"></path>
        <path fill-rule="evenodd" clip-rule="evenodd" d="M4 1C4 0.447715 4.44772 0 5 0H12V7C12 7.55228 11.5523 8 11 8C10.4477 8 10 7.55228 10 7V2H5C4.44772 2 4 1.55228 4 1Z"></path>
    </svg>
</button>
~~~

Параметры:
* `data-gallery="true"` - обязательный, определяет вид вызываемого попапа, в данном случае галерею
* `data-src="{String}"` - обязательный, путь к полноразмерному изображению, возможно исходному
* `data-alt="{String}"` - необязательный, alt изображения
* `data-caption="{String}"` - необязательный, подпись изображения
* `data-slider={ID(String|Number)}` - идентификатор галереи, у всех кнопок одной галереи должен быть одинаковый идентификатор, у разных галерей - разный

## Попап Хода строительства

1. Вызов идёт с карточек хода строительства:

~~~html
<article class="progress-card j-popup" 
         data-progress="true"
         data-ajax="/tests/progress.json"
         data-query="GET"
         data-id="1a">
    <!--...-->
</article>
~~~

Параметры:
* `data-progress="true"` - обязательный, определяет вид вызываемого попапа, в данном случае ход строительства
* `data-ajax="{String}"` - обязательный, путь ajax-запроса
* `data-query="[GET|POST]"` - необязательный, по умолчанию POST
* `data-id="{String}"` - обязательный, id альбома хода строительства

Структура ответа сервера в файле: public/tests/progress.json

**Важно: альбомы в ответе сервера необходимо присылать в том же порядке, в котором они выведены на вёрстке**

## Попап Онлайн-трансляции

1. Вызов идёт с кнопки онлайн-трансляции:

~~~html
<button type="button"
        class="button j-popup"
        data-ajax-online="true"
        data-ajax="/tests/online.json"
        data-query="GET">
    <span>Онлайн-трансляции</span>
</button>
~~~

Параметры:
* `data-ajax-online="true"` - обязательный, определяет вид вызываемого попапа, в данном случае онлайн-трансляции
* `data-ajax="{String}"` - обязательный, путь ajax-запроса
* `data-query="[GET|POST]"` - необязательный, по умолчанию POST

Структура ответа сервера в файле: public/tests/online.json

## Попап Форм обратной связи

1. Вызов идёт с кнопок, оформленных следующим образом:

~~~html
<button type="button"
        class="button header__callback-button j-popup-callback"
        data-callback="true"
        data-href="callback"
        aria-label="Заказать звонок">
    <span>Заказать звонок</span>
</button>
~~~

Параметры:
* `data-callback="true""` - обязательный, определяет вид вызываемого попапа, в данном случае попап с формами
* `data-href="{Id}"` - обязательный, id шаблона формы, которая будет вставлена в попап

2. Шаблон формы:
* Может быть вставлен как внутрь компонента, так и после футера сайта, основное условие - уникальный id
* Находится в процессе разработки

~~~html
<template id="callback">
    <div class="form">
        <h4>Заказ звонка</h4>
        <p>Менеджер перезвонит вам и&nbsp;ответит на&nbsp;вопросы</p>
        <!-- todo: сделать форму -->
        <p>тут будет форма</p>
    </div>
</template>
~~~
