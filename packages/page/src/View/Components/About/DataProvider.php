<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\About;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Kelnik\Core\View\Components\Contracts\HasMargin;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\View\Components\About\Layouts\ContentLayout;
use Kelnik\Page\View\Components\About\Layouts\SettingsLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    public const BUTTON_TEXT_LIMIT = 100;
    public const BUTTON_LINK_LIMIT = 150;

    public function getEditLayouts(): array
    {
        $buttons = Button::make(trans('kelnik-page::admin.save'))
                        ->icon('bs.save')
                        ->class('btn btn-secondary')
                        ->method('saveData');

        return [
            Layout::tabs([
                 trans('kelnik-page::admin.componentData.headers.content') => new ContentLayout($buttons),
                 trans('kelnik-page::admin.componentData.headers.settings') => new SettingsLayout($buttons),
//                trans('kelnik-page::admin.componentData.headers.design') => new ThemeLayout(
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
                'validate' => 'nullable|string|max:255'
            ],
            [
                'name' => 'text',
                'type' => 'string',
                'validate' => 'nullable|string'
            ],
            [
                'name' => 'factoids',
                'type' => 'array',
                'validate' => 'nullable|array'
            ],
            [
                'name' => 'alias',
                'type' => 'string',
                'validate' => 'nullable|max:150|regex:/[a-z0-9\-_]+/i'
            ],
            [
                'name' => 'textOnRight',
                'type' => 'bool',
                'validate' => 'boolean'
            ],
            [
                'name' => 'button',
                'type' => 'array',
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
        $validateRules['data.margin.*'] = 'numeric|min:' . HasMargin::MARGIN_MIN . '|max:' . HasMargin::MARGIN_MAX;

        $request->validate($validateRules);
    }

    public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
    {
        // Content
        $content = [];

        foreach ($this->getContentFields() as $field) {
            $content[$field['name']] = $request->input('data.content.' . $field['name']);
        }

        $content['factoids'] = array_values($content['factoids'] ?? []);

        $this->data->put('content', $content);
        $this->data->put('margin', array_map('intval', $request->input('data.margin', [])));
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
