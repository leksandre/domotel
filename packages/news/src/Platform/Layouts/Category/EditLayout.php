<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Layouts\Category;

use Illuminate\Support\Arr;
use Kelnik\Core\Platform\Fields\Slug;
use Kelnik\Core\Services\Contracts\CoreService;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class EditLayout extends Rows
{
    protected function fields(): array
    {
        /** @var CoreService $coreService */
        $coreService = $this->query->get('coreService');
        $res = [
            Input::make('category.title')
                ->title('kelnik-news::admin.title')
                ->id('field-news-title')
                ->maxlength(255)
                ->required(),
//            Slug::make('category.slug')
//                ->title('kelnik-news::admin.slug')
//                ->maxlength(255)
//                ->required()
//                ->source('field-news-title')
//                ->method('transliterate'),
            Switcher::make('category.active')->title('kelnik-news::admin.active')->sendTrueOrFalse(),
            Input::make('category.priority')
                ->type('number')
                ->title('kelnik-news::admin.priority')
                ->maxlength(3)
                ->value(500)
                ->step(10)
                ->hr()
        ];

        if ($coreService->hasModule('page')) {
            $pageIds = $this->query->get('pageIds');

            /** @var array $site */
            foreach ($this->query->get('pageOptions') as $site) {
                $res[] = Select::make('page.' . $site['id'])
                    ->title(trans('kelnik-news::admin.modelToPage', ['site' => $site['title']]))
                    ->help('kelnik-news::admin.modelToPageHelp')
                    ->value(
                        Arr::get($pageIds, $site['id'] . '.' . $this->query->get('category')->getKey())
                    )
                    ->options($site['pages']);
            }
        }

        $res[] = Button::make(trans('kelnik-news::admin.save'))
            ->icon('bs.save')
            ->class('btn btn-secondary')
            ->method('saveCategory');

        return $res;
    }
}
