# Модуль "Генератор PDF" (`pdf`)

## Возможности
* Генерация PDF из html страницы или URL
* Сжатие и оптимизация PDF через [ghostscript](https://www.ghostscript.com/)

## Драйверы

### Генератор

* [Chrome Devtools Protocol](https://chromedevtools.github.io/devtools-protocol/)
* [Chromium](https://www.chromium.org/Home/) (через cli)
* [Wkhtmltopdf](https://wkhtmltopdf.org/) (через cli)

### Сжатие

* [gs](https://www.ghostscript.com/)
* [ps2pdf](https://www.ghostscript.com/) (обертка над `gs`)

## Задачи
* `DeleteOldFiles` - удаляет старые файлы pdf, срок жизни, которых превышен.<br>Срок жизки указывется в конфиге, секция `cache`.

## Расписание задач
* Каждую среду и субботу в 03:00 запуск `DeleteOldFiles`

Настроить расписание можно в конфиге модуля.

## Использование

### Генерация PDF в виде файла

```php
/** @var \Kelnik\Pdf\Services\Contracts\PdfService $pdfService */
$pdfService = resolve(PdfService::class);

/** @var \Kelnik\Pdf\Services\Contracts\PdfFileResponse $pdf */
$pdf = $pdfService->printToFile('estate', 'flat-1.pdf', $html, $cacheTags);

// Отправить файл пользователю
$pdf->download()->send();

// Можно указать название файла
$pdf->download('some-name.pdf')->send();
```

При использовании данного варианта генерации файлы сохраняются в хранилище `app/pdf` (редактируется в конфиге).
Срок хранения файлов - неделя (также редактируется в конфиге). Также в кеш сохраняется теги и информация о файле.

Также сервис производит поиск атрибутов `src="..."` и `url(...)`.
Если содержимое является абсолютным путем к файлу, то содержимое такого атрибута заменяется на ссылку вида `data:url`.
Все остальные варианты содержимого игнорируются и не изменяются.

Например: `src="/images/plan.jpg"` будет преобразован в `src="data:image/jpeg;base64,..."`.
А вариант `src="http://host.com/images/plan2.jpg"` останется без изменений.

Чтобы проверить, есть ли требуемый файл в кеше, необходимо вызвать метод `getFileByPath`.

```php
/** @var ?PdfFileResponse $pdf */
$pdf = $pdfService->getFileByPath('estate', 'flat-1.pdf');
```

### Генерация PDF как строку

```php
/** @var \Kelnik\Pdf\Services\Contracts\PdfService $pdfService */
$pdfService = resolve(PdfService::class);
$pdf = $pdfService->printToBinary($html);

// Либо сразу получить закодированную строку в base64
// $pdf = $pdfService->printToBase64($html);

// Вернуть ответ на запрос как PDF файл
response($pdf)
        ->header('Content-type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="some-name.pdf"')
        ->send();

// Либо сохранить как файл в хранилище
\Illuminate\Support\Facades\Storage::putFile('some-name.pdf', $pdfAsBinary);
```

При использовании данного варианта результат не кешируется.
