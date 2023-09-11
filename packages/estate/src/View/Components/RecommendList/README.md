## Компонент "Список рекомендованных помещений"

Выводит ограниченный список записей без пагинации на основе переданных данных.

* Количество записей и заголовок блока указывается в настройках компонента
* Фильтр для выборки данных похожишх квартир формируется на основе данных из буфера компонентов страницы `Kelnik\Page\Models\Contracts\BufferDto` 
и его реализациия `Kelnik\Estate\View\Components\PremisesCard\PremisesCardBufferDto`. Если буфер отсутсвует, то список не выводится.
* Возможность выбрать шаблон для отображения. Список доступных шаблонов в методе `RecommendList::getTemplates()`