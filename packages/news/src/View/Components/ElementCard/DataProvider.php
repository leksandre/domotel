<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\ElementCard;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Kelnik\News\View\Components\ElementCard\Layouts\RoutesLayout;
use Kelnik\News\View\Components\ElementCard\Layouts\SettingsLayout;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\PageComponent;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    public function getEditLayouts(): array
    {
        $buttons = Button::make(trans('kelnik-news::admin.save'))
                        ->icon('bs.save')
                        ->class('btn btn-secondary')
                        ->method('saveData');

        return [
            new SettingsLayout(),
            new RoutesLayout(),
            Layout::rows([$buttons])
        ];
    }

    public function getComponentTitle(): string
    {
        return $this->data->get('title') ?? parent::getComponentTitle();
    }

    public function validateSavingRequest(PageComponent $pageComponent, Request $request): void
    {
        $request->validate([
            'data.title' => 'nullable|max:255',
            'data.other.title' => 'nullable|max:255',
            'data.other.count' => 'numeric|min:0|max:10',
            'data.template' => 'required|max:150|regex:/[a-z0-9\-_]+/i'
        ]);
    }

    public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
    {
        $this->data = new Collection([
            'title' => $request->input('data.title'),
            'template' => $request->input('data.template'),
            'other' => [
                'title' => $request->input('data.other.title'),
                'count' => (int)$request->input('data.other.count')
            ]
        ]);
    }

    public function setDefaultValue(): void
    {
        $this->data = new Collection([
            'title' => $this->getComponentTitleOriginal(),
            'template' => $this->componentNamespace::getTemplates()->first()?->name,
            'other' => [
                'title' => trans('kelnik-news::front.elementCard.otherListTitle'),
                'count' => config('kelnik-news.card.otherElementsCount')
            ]
        ]);
    }
}
