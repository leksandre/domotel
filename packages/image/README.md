# Модуль обработки изображений (`image`)

## Возможности
* Оптимизация изображений
* Использование драйверов: Imagick, GD2, [Vips*](https://libvips.github.io/libvips/)
* Поддержка дополнительных форматов: WEBP, AVIF*
* Изменение размера по параметрам

`*` - в разработке

## Использование

### Создание тега `picture`

```php
$picture = \Kelnik\Image\Picture::init($fileId);

return view('template_name', ['picture' => $picture->render()]);
```

С указанием своих параметров.

```php
$picture = \Kelnik\Image\Picture::init($fileId);
$picture->setBreakpoints([1921 => 2560, 1281 => 1920, 769 => 1280])
    ->setImageAttribute('alt', 'picture alt text')
    ->setPictureAttribute('class', 'some_class');

return view('template_name', ['picture' => $picture->render()]);
```

### Картинка определенного размера

```php
$attachment = resolve(AttachmentRepository::class)->findByPrimary($fileId);
abort_if(!$attachment || !$attachment->exists, Response::HTTP_NOT_FOUND);
$imageFile = new \Kelnik\Image\ImageFile($attachment);
$params = new \Kelnik\Image\Params($imageFile);
$imagePath = \Kelnik\Image\Picture::getResizedPath($imageFile, $params);

return view('template_name', ['image' => $imagePath]);
```

### Создание копии изображения

```php
$params = new \Kelnik\Image\Params($imageFile);
$params->width = 500;
$params->height = 200;

$imageConfig = new \Kelnik\Image\Config();
$src = '/var/www/public/source_image.jpg';

(new \Kelnik\Image\Resizer($src, $imageConfig))
    ->setParams($params)
    ->save('/var/www/public/' . $params->filename);
```
