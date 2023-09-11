<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\ErrorInfo\Layouts;

use Closure;
use Illuminate\Support\Arr;
use Kelnik\Core\Platform\Fields\MatrixStatic;
use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\Services\Contracts\HttpErrorService;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

final class ContentLayout extends Rows
{
    public function __construct(private Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        /** @var HttpErrorService $httpErrorService */
        $matrixData = [];
        $curData = $this->query->get('data');
        $httpErrorService = resolve(HttpErrorService::class);

        foreach ($httpErrorService::EXCEPTIONS as $statusCode => $exceptionClass) {
            $matrixData[$statusCode] = Arr::get($curData, 'content.text.' . $statusCode, []);

            if (!empty($matrixData[$statusCode])) {
                $matrixData[$statusCode]['code'] = $statusCode;
                continue;
            }

            $matrixData[$statusCode] = [
                'code' => $statusCode,
                'title' => trans('kelnik-page::admin.components.errorInfo.state.' . $statusCode . '.title'),
                'text' => trans('kelnik-page::admin.components.errorInfo.state.' . $statusCode . '.text')
            ];
        }
        $curData['content']['text'] = $matrixData;
        $this->query->set('data', $curData);
        unset($matrixData);

        return [
            Input::make('data.content.title')
                ->title('kelnik-page::admin.components.errorInfo.titleField')
                ->maxlength(255)
                ->hr(),
            Picture::make('data.content.background')
                ->title('kelnik-page::admin.components.errorInfo.background')
                ->groups(PageServiceProvider::MODULE_NAME)
                ->targetId(),
            Title::make('')->value(trans('kelnik-page::admin.components.errorInfo.buttons.headHome')),
            Input::make('data.content.buttons.0.title')
                ->title('kelnik-page::admin.components.errorInfo.buttons.title')
                ->placeholder('kelnik-page::admin.components.errorInfo.buttons.titleHome')
                ->maxlength(255)
                ->help('kelnik-page::admin.components.errorInfo.buttons.help'),
            Input::make('data.content.buttons.0.url')
                ->title('kelnik-page::admin.components.errorInfo.buttons.url')
                ->maxlength(255),
            Title::make('')->value(trans('kelnik-page::admin.components.errorInfo.buttons.headSearch')),
            Input::make('data.content.buttons.1.title')
                ->title('kelnik-page::admin.components.errorInfo.buttons.title')
                ->placeholder('kelnik-page::admin.components.errorInfo.buttons.titleSearch')
                ->maxlength(255)
                ->help('kelnik-page::admin.components.errorInfo.buttons.help'),
            Input::make('data.content.buttons.1.url')
                ->title('kelnik-page::admin.components.errorInfo.buttons.url')
                ->maxlength(255)
                ->hr(),
            Title::make('')->value(trans('kelnik-page::admin.components.errorInfo.headText')),
            MatrixStatic::make('data.content.text')
                ->title('kelnik-page::admin.components.errorInfo.text.header')
                ->columns([
                    trans('kelnik-page::admin.components.errorInfo.text.code') => 'code',
                    trans('kelnik-page::admin.components.errorInfo.text.title') => 'title',
                    trans('kelnik-page::admin.components.errorInfo.text.text') => 'text'
                ])
                ->fields([
                    'code' => Title::make('')->style('margin: 5px'),
                    'title' => Input::make(),
                    'text' => TextArea::make()->style('height: 100px !important')
                ]),

            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
