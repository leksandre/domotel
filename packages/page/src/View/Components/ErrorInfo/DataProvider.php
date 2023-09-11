<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\ErrorInfo;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Contracts\DeleteAttach;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Services\Contracts\HttpErrorService;
use Kelnik\Page\View\Components\ErrorInfo\Layouts\ContentLayout;
use Kelnik\Page\View\Components\ErrorInfo\Layouts\SettingsLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    use DeleteAttach;

    public function getEditLayouts(): array
    {
        $buttons = Button::make(trans('kelnik-page::admin.save'))
                        ->icon('bs.save')
                        ->class('btn btn-secondary')
                        ->method('saveData');

        return [
            Layout::tabs([
                 trans('kelnik-page::admin.componentData.headers.content') => new ContentLayout($buttons),
                 trans('kelnik-page::admin.componentData.headers.settings') => new SettingsLayout($buttons)
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

    public function getContentFields(): Collection
    {
        return new Collection([
            [
                'name' => 'title',
                'type' => 'string',
                'validate' => 'required|string|max:255'
            ],
            [
                'name' => 'background',
                'validate' => 'nullable|numeric'
            ],
            [
                'name' => 'text',
                'validate' => 'required|array'
            ],
            [
                'name' => 'buttons',
                'validate' => 'nullable|array'
            ]
        ]);
    }

    public function validateSavingRequest(PageComponent $pageComponent, Request $request): void
    {
        $validateRules = [];
        $this->getContentFields()->each(static function ($el) use (&$validateRules) {
            if (isset($el['validate'])) {
                $validateRules['data.content.' . $el['name']] = $el['validate'];
            }
        });

        $validateRules['data.content.buttons.*.title'] = 'nullable|max:255';
        $validateRules['data.content.buttons.*.url'] = 'nullable|max:255';

        $request->validate($validateRules);
    }

    public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
    {
        // Content
        $content = [];
        $oldAttachIds = $this->getAttachIds($this->data);

        foreach ($this->getContentFields() as $field) {
            $content[$field['name']] = $request->input('data.content.' . $field['name']);
        }

        $this->data->put('content', $content);

        $newAttachIds = $this->getAttachIds($this->data);

        if ($oldAttachIds) {
            self::$deleteAttachIds = array_diff($oldAttachIds, $newAttachIds);
        }
    }

    public function setDefaultValue(): void
    {
        $res = [
            'content' => [
                'title' => $this->getComponentTitleOriginal()
            ]
        ];

        /** @var HttpErrorService $httpErrorService */
        $httpErrorService = resolve(HttpErrorService::class);

        foreach ($httpErrorService::EXCEPTIONS as $statusCode => $exceptionClass) {
            $res['content']['text'][$statusCode] = [
                'title' => trans('kelnik-page::admin.components.errorInfo.state.' . $statusCode . '.title'),
                'text' => trans('kelnik-page::admin.components.errorInfo.state.' . $statusCode . '.text')
            ];
        }
        unset($httpErrorService);

        $this->data = new Collection($res);
    }

    protected function getAttachIds(Collection $originData): array
    {
        return array_filter([
            (int)Arr::get($originData, 'content.background')
        ]);
    }
}
