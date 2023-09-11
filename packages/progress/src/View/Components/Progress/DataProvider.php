<?php

declare(strict_types=1);

namespace Kelnik\Progress\View\Components\Progress;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Progress\Services\Contracts\ProgressService;
use Kelnik\Progress\View\Components\Progress\Layouts\ContentLayout;
use Kelnik\Progress\View\Components\Progress\Layouts\SettingsLayout;
use Kelnik\Progress\View\Components\Progress\Requests\SaveDataRequest;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    public function getEditLayouts(): array
    {
        $buttons = Button::make(trans('kelnik-progress::admin.save'))
                        ->icon('bs.save')
                        ->class('btn btn-secondary')
                        ->method('saveData');

        /** @var ProgressService $progressService */
        $progressService = resolve(ProgressService::class);

        return [
            Layout::tabs([
                 trans('kelnik-progress::admin.components.progress.tabs.content') => new ContentLayout(
                     $buttons,
                     $progressService->getContentGroupsLink(),
                     $progressService->getContentCamerasLink(),
                     $progressService->getContentAlbumsLink()
                 ),
                 trans('kelnik-progress::admin.components.progress.tabs.settings') => new SettingsLayout($buttons),
//                trans('kelnik-news::admin.componentData.headers.design') => new ThemeLayout(
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
