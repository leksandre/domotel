# Валидатор 3000
**Проверяет на валидацию все формы и инпуты с аттрибутом "data-validate"**

### Что нужно знать:
- Инициализовать лучше в первую очередь. До обработчиков на формы и инпуты!
- Если инпут уже в форме с аттрибутом ```data-validate```, добавлять инпуту аттр не обязательно!
- После успешной валидности (форме|инпуту) дается аттрибут ```validity```. По этому аттрибуту проводить проверку на отправку 
- Для инициализации формы или инпутов после "ajax" есть метод ```update```
- Инпутам с типом tel ```<input type='tel'>``` дается маски телефона
- Инпутам с типом number ```<input type='tel'>``` дается маски телефона. Для типов: ```email``` ```number``` ```url``` задается страндартная валидация 

#####Классы:
- ```validate-form-success``` - для формы, при успешной валидации всей формы
- ```validate-form-error``` - для формы, при неудачной валидации всей формы
- ```validate-input-success``` - для инпута, при успешной валидации инпута
- ```validate-input-error``` - для инпута, при неудачной валидации инпута
- ```validate-input-key``` - для инпута, при редактировании инпута, удаляется через опр. промежуток времени

####Аттрибуты (основные):
- ```data-validate``` - Для инциализации (формы|инпута)
- ```data-validate-check```="(key|submit)" - Отпределяет тип проверки валидации для формы (только для формы)
- ```data-validate-name``` - Имя поля для вывода ошибки (инпуту)
- ```data-validate-error``` - Имя поля для вывода ошибки (другому любому тегу)

####Аттрибуты (Валидации):
- ```data-validate-required``` - Обязательное поле
- ```data-validate-mask=""``` - Задает маску
- ```data-validate-regexp=""``` - Валидация по regexp'у
- ```data-validate-email``` - Эл. почта
- ```data-validate-number``` - Ввод только чисел
- ```data-validate-letters``` - Ввод только букв (кирилица, латиница)
- ```data-validate-url``` - Адрес веб-страницы
- ```data-validate-min=""``` - Мин кол-во символов
- ```data-validate-max=""``` - Макс кол-во символов
> Вывод кастомного сообщения об ошибки через аттрибут ```data-validate-ANYTHING-msg='Your message'```

> Что бы в кастомное сообщение поствить значение от аттрибута валидации нужно указать символ ```$```, Символ заменится значением аттрибута. Ex: ```data-validate-min-msg=" Надо не меньше $ символов!"```
####Аттрибуты (Ограничивающие ввод, форматирование)
- ```data-validate-key-only=""``` - Ввод только опр. символо по charCode, значение через запятую, дапозон вводится через тире. Ex: ```='48-57, 46'``` 
- ```data-validate-price="separator"``` - Ввод только числа, преобразует число в ценовой формат. Ex: ```1 234 567```. ```separator``` - разделитель тысячных **(не обязательный)**.
- ```data-validate-decimal="separator"``` - Ввод только числа и разделитель. При ```focusout``` добавляет после разделителя нули. Ex: ```1,130```
- ```data-validate-decimal-digits=""``` - Кол-во знаков после разделителя. **Только при ```data-validate-decimal```**

###Примеры

Custom mask "aa-9{1,4}" + required + custom error messages
```
<span data-validate-error="mask"></span>
<input type="text"
       placeholder="ka-1234"
       data-validate
       data-validate-name="mask"
       data-validate-required="Очень обязательна"
       data-validate-required-msg="Маска обязательна! $!"
       data-validate-mask="aa-9{1,4}"
       data-validate-mask-msg="Первые две буква потом числа! Mask: $">
```

Regexp "/^[5-9]+$/"
```
<span data-validate-error="reg"></span>
<input type="text"
       placeholder="56789"
       data-validate
       data-validate-name="reg"
       data-validate-regexp="/^[5-9]+$/">
```

Email + required
```
<span data-validate-error="email"></span>
<input type="text"
       placeholder="ex@mail.com"
       data-validate
       data-validate-name="email"
       data-validate-required
       data-validate-email>
```

Min 2, Max 3, Number + custom error messages
```
<span data-validate-error="number"></span>
<input type="text"
       placeholder="983"
       data-validate
       data-validate-name="number"
       data-validate-min="2"
       data-validate-min-msg="Мин. $ числа!"
       data-validate-max="3"
       data-validate-max-msg="Макс. $ числа!"
       data-validate-number>
```

Letters, Min 2, Max 30
```
<span data-validate-error="letters"></span>
<input type="text"
       placeholder="Hello"
       data-validate
       data-validate-name="letters"
       data-validate-min="2"
       data-validate-max="30"
       data-validate-letters>
```

URL
```
<span data-validate-error="url"></span>
<input type="text"
       placeholder="google.com"
       data-validate
       data-validate-name="url"
       data-validate-url>
```

Tel + required + custom error message
```
<span data-validate-error="phone"></span>
<input type="tel"
       placeholder="+7 (___) ___ __-__"
       data-validate
       data-validate-name="phone"
       data-validate-required
       data-validate-required-msg="Номер телефона обязателен!"
       data-validate-mask-msg="Заполните номер телефона полностью">
```

Checkbox + custom error messages
```
<div>
    <span data-validate-error="checkbox"></span>

    <input type="checkbox"
           id="checkbox"
           data-validate
           data-validate-name="checkbox"
           data-validate-required
           data-validate-required-msg="Согласитель с персональными данными!">
    <label for="checkbox">
        <span>Person data</span>
    </label>
</div>
```

Radio required.
```
<form action="#" 
      data-validate 
      data-validate-check="submit">

    <div data-validate-error="radio"></div>
    <label>
        radio 1
        <input type="radio"
               name="radio"
               data-validate-name="radio"
               data-validate-required>
    </label>

    <label>
        radio 2
        <input type="radio"
               name="radio"
               data-validate-name="radio"
               data-validate-required>
    </label>

    <label>
        radio 3
        <input type="radio"
               name="radio"
               data-validate-name="radio"
               data-validate-required
               data-validate-required-msg="Выберите один из вариантов!">
    </label>

    <button type="submit">Submit</button>
</form>
```

Форма
```
<form data-validate 
      data-validate-check="key">

       <div data-validate-error="surname"></div>
       <input type="text"
              name="surname"
              placeholder="name"
              data-validate-name="surname"
              data-validate-required
              data-validate-required-msg="Введите ваше Имя!"
              data-validate-regexp="/^[а-яё]+$/i"
              data-validate-regexp-msg="Имя только на кирилице"
              data-validate-min="2"
              data-validate-max="30">

       <div data-validate-error="tel"></div>
       <input type="tel"
              name="tel"
              placeholder="Phone"
              data-validate-name="tel"
              data-validate-required>

   <button type="submit">Submit</button>
</form>
```

Ввод тольлько опр. символов (charCode: 48-57, 46)
```
<div data-validate-error="onlyNumbers"></div>
<input type="text"
       placeholder="Числа и точки"
       data-validate
       data-validate-name="onlyNumbers"
       data-validate-key-only="48-57, 46"
       data-validate-regexp="/^\d+(.\d+)*?$/"
       data-validate-regexp-msg="Начало и конец цифры! и не больше друх точек подряд!">
```

Ввод тольлько опр. символов (charCode: 65-90, 97-122, 32)
```
<div data-validate-error="onlyLetters"></div>
<input type="text"
       placeholder="English letters & space"
       data-validate
       data-validate-name="onlyLetters"
       data-validate-key-only="65-90, 97-122, 32"
       data-validate-min="5"
       data-validate-max="30">
```

Формат цены
```
<div data-validate-error="price"></div>
<input type="text"
       placeholder="Формат цены"
       data-validate
       data-validate-name="price"
       data-validate-required
       data-validate-price=>

<input type="text"
       placeholder="Формат цены с точкой"
       data-validate
       data-validate-price=".">
```

Формат Десятичных
```
<div data-validate-error="decimal"></div>
<input type="text"
       placeholder="Формат десытичных"
       data-validate
       data-validate-name="decimal"
       data-validate-required
       data-validate-decimal>

<input type="text"
       placeholder="Формат десытичных"
       data-validate
       data-validate-decimal="."
       data-validate-decimal-digits="3">
```
