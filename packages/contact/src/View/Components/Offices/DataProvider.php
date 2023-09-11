<?php

declare(strict_types=1);

namespace Kelnik\Contact\View\Components\Offices;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Kelnik\Contact\Services\Contracts\ContactService;
use Kelnik\Contact\View\Components\Offices\Layouts\ContentLayout;
use Kelnik\Contact\View\Components\Offices\Layouts\SettingsLayout;
use Kelnik\Contact\View\Components\Offices\Requests\SaveDataRequest;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\PageComponent;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    public function getEditLayouts(): array
    {
        $buttons = Button::make(trans('kelnik-contact::admin.save'))
                        ->icon('bs.save')
                        ->class('btn btn-secondary')
                        ->method('saveData');

        return [
            Layout::tabs([
                 trans('kelnik-contact::admin.component.headers.content') => new ContentLayout(
                     $buttons,
                     resolve(ContactService::class)->getContentLink()
                 ),
                 trans('kelnik-contact::admin.component.headers.settings') => new SettingsLayout($buttons),
//                trans('kelnik-contact::admin.componentData.headers.design') => new ThemeLayout(
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
        $data = resolve(SaveDataRequest::class)->getData();
        $this->data->put('content', $data['content']);
        $this->data->put('map', $data['map']);
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
