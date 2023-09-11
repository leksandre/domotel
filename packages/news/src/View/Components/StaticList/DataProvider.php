<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\StaticList;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Kelnik\News\View\Components\StaticList\Layouts\ContentLayout;
use Kelnik\News\View\Components\StaticList\Layouts\SettingsLayout;
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
            Layout::tabs([
                 trans('kelnik-news::admin.component.headers.content') => new ContentLayout($buttons),
                 trans('kelnik-news::admin.component.headers.settings') => new SettingsLayout($buttons),
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

    public function getContentFields(): Collection
    {
        return new Collection([
            [
                'name' => 'title',
                'type' => 'string',
                'validate' => 'nullable|max:255'
            ],
            [
                'name' => 'alias',
                'type' => 'string',
                'validate' => 'nullable|max:150|regex:/[a-z0-9\-_]+/i'
            ],
            [
                'name' => 'categories',
                'type' => 'array',
                'validate' => 'nullable|array'
            ],
            [
                'name' => 'limit',
                'type' => 'int',
                'validate' => 'numeric'
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

        $validateRules['alias'] = 'nullable|max:150|regex:/[a-z0-9\-_]+/i';
        $validateRules['data.template'] = 'required|max:150|regex:/[a-z0-9\-_]+/i';

        $request->validate($validateRules);
    }

    public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
    {
        // Content
        $content = [];

        foreach ($this->getContentFields() as $field) {
            $content[$field['name']] = $request->input('data.content.' . $field['name']);
        }

        $content['limit'] = $content['limit'] ? (int)$content['limit'] : 1;

        $content['categories'] = $content['categories']
                                    ? array_map('intval', $content['categories'])
                                    : [];

        $this->data->put('content', $content);

        // Template
        $this->data->put('template', trim($request->input('data.template') ?? ''));
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
