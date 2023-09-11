<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\StaticList\Layouts;

use Closure;
use Kelnik\News\Platform\Services\Contracts\NewsPlatformService;
use Kelnik\News\Services\Contracts\NewsService;
use Kelnik\Page\Models\Page;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

final class ContentLayout extends Rows
{
    private readonly NewsService $newsService;
    private readonly NewsPlatformService $newsPlatformService;

    public function __construct(private Fieldable|Groupable|Closure $tabFooter)
    {
        $this->newsService = resolve(NewsService::class);
        $this->newsPlatformService = resolve(NewsPlatformService::class);
    }

    protected function fields(): array
    {
        /** @var Page $page */
        $page = $this->query->get('page');
        $categories = $this->newsService->getAllCategories()?->pluck('title', 'id');

        return [
            Input::make('data.content.title')
                ->title('kelnik-news::admin.components.staticList.titleField')
                ->placeholder('kelnik-news::admin.components.staticList.titlePlaceholder')
                ->maxlength(255),

            Select::make('data.content.categories')
                ->title('kelnik-news::admin.components.staticList.categories')
                ->options($categories)
                ->help('kelnik-news::admin.components.staticList.categoriesHelp')
                ->multiple(),

            Input::make('data.content.limit')
                ->title('kelnik-news::admin.components.staticList.limit')
                ->type('number')
                ->min(1)
                ->max(15)
                ->help(trans(
                    'kelnik-news::admin.components.staticList.limitHelp',
                    [
                        'start' => 1,
                        'finish' => 15
                    ]
                ))
                ->addBeforeRender(function () {
                    if ($this->get('value') === null) {
                        $this->set('value', config('kelnik-news.pagination.limit'));
                    }
                }),

            $this->newsPlatformService->getContentLink(),

            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
