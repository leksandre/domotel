<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Layouts\Element;

use Illuminate\Support\Carbon;
use Kelnik\Core\Platform\Fields\Slug;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\News\Models\Category;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class BaseLayout extends Rows
{
    protected function fields(): array
    {
        $element = $this->query->get('element');

        return [
            Relation::make('element.category_id')
                ->title('kelnik-news::admin.category')
                ->fromModel(Category::class, 'title')
                ->value($this->query->get('category_id'))
                ->required(),
            Input::make('element.priority')
                ->type('number')
                ->title('kelnik-news::admin.priority')
                ->maxlength(3)
                ->value(500)
                ->step(10),
            Switcher::make('element.active')
                ->title('kelnik-news::admin.active')
                ->sendTrueOrFalse(),
            Input::make('element.title')
                ->title('kelnik-news::admin.title')
                ->id('field-news-title')
                ->maxlength(255)
                ->required(),
            Slug::make('element.slug')
                ->title('kelnik-news::admin.slug')
                ->maxlength(255)
                ->required()
                ->source('field-news-title')
                ->method('transliterate')
                ->hr(),

            Title::make('')->value(trans('kelnik-news::admin.activeDateRange')),

            DateTimer::make('element.active_date_start')
                ->title('kelnik-news::admin.dateStart')
                ->placeholder('kelnik-news::admin.selectDate')
                ->min(Carbon::create(1970, 1, 1))
                ->enableTime()
                ->format24hr()
                ->allowInput()
                ->allowEmpty(),
            DateTimer::make('element.active_date_finish')
                ->title('kelnik-news::admin.dateFinish')
                ->placeholder('kelnik-news::admin.selectDate')
                ->min(Carbon::create(1970, 1, 1))
                ->enableTime()
                ->format24hr()
                ->allowInput()
                ->allowEmpty()
                ->hr(),

            Title::make('')->value(trans('kelnik-news::admin.publishDateRange')),

//            DateTimer::make('element.publish_date_start')
//                ->title('kelnik-news::admin.dateStart')
//                ->placeholder('kelnik-news::admin.selectDate')
//                ->min(Carbon::create(1970, 1, 1))
//                ->enableTime()
//                ->format24hr()
//                ->allowInput()
//                ->allowEmpty(),
            DateTimer::make('element.publish_date_finish')
                ->title('kelnik-news::admin.dateFinish')
                ->placeholder('kelnik-news::admin.selectDate')
                ->min(Carbon::create(1970, 1, 1))
                ->enableTime()
                ->format24hr()
                ->allowInput()
                ->allowEmpty(),
//            Switcher::make('element.show_timer')
//                ->title('kelnik-news::admin.showTimer')
//                ->sendTrueOrFalse()
//                ->hr(),

            DateTimer::make('element.publish_date')
                ->title('kelnik-news::admin.publishDate')
                ->placeholder('kelnik-news::admin.selectDate')
                ->min(Carbon::create(1970, 1, 1))
                ->enableTime()
                ->format24hr()
                ->allowInput()
                ->allowEmpty()
                ->addBeforeRender(function () use ($element) {
                    if (!$element->exists && !$this->get('value')) {
                        $this->set('value', now());
                    }
                }),

            Button::make(trans('kelnik-news::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveElement')
        ];
    }
}
