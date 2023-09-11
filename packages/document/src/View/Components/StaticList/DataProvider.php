<?php

declare(strict_types=1);

namespace Kelnik\Document\View\Components\StaticList;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Kelnik\Document\Services\Contracts\DocumentService;
use Kelnik\Document\View\Components\StaticList\Layouts\ContentLayout;
use Kelnik\Document\View\Components\StaticList\Layouts\SettingsLayout;
use Kelnik\Document\View\Components\StaticList\Requests\SaveDataRequest;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\PageComponent;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    public function getEditLayouts(): array
    {
        $buttons = Button::make(trans('kelnik-document::admin.save'))
                        ->icon('bs.save')
                        ->class('btn btn-secondary')
                        ->method('saveData');

        return [
            Layout::tabs([
                 trans('kelnik-document::admin.component.headers.content') => new ContentLayout(
                     $buttons,
                     resolve(DocumentService::class)->getContentLink(),
                 ),
                 trans('kelnik-document::admin.component.headers.settings') => new SettingsLayout($buttons),
//                trans('kelnik-document::admin.componentData.headers.design') => new ThemeLayout(
//                    $this->getThemeFields(),
//                    $buttons
//                )
            ])
        ];
    }

    protected function getThemeFields(): Collection
    {
        return collect();
    }

    public function getComponentTitle(): string
    {
        return $this->data->get('content')['title'] ?? parent::getComponentTitle();
    }

    public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
    {
        $this->data->put('content', resolve(SaveDataRequest::class)->getData());
    }

    public function setDefaultValue(): void
    {
        $this->data = new Collection([
            'content' => [
                'title' => $this->getComponentTitleOriginal()
            ]
        ]);
    }
}
