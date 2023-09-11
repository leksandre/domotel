<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Stub;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Core\Theme\Color;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Contracts\DeleteAttach;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Platform\Layouts\ComponentSettings\ThemeLayout;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Stub\Layouts\ContentLayout;
use Kelnik\Page\View\Components\Stub\Layouts\SettingsLayout;
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
                trans('kelnik-page::admin.componentData.headers.settings') => new SettingsLayout($buttons),
                trans('kelnik-page::admin.componentData.headers.design') => new ThemeLayout(
                    $this->getThemeFields(),
                    $buttons
                )
            ])
        ];
    }

    protected function getThemeFields(): Collection
    {
        return new Collection([
            'colors' => new Collection([
                new Color(
                    name: 'brand-base',
                    title: trans('kelnik-page::admin.components.stub.colors.brand-base')
                ),
                new Color(
                    name: 'white',
                    title: trans('kelnik-page::admin.components.stub.colors.white')
                ),
                new Color(
                    name: 'black-rgb',
                    title: trans('kelnik-page::admin.components.stub.colors.black-rgb') . ' (RGB)'
                )
            ])
        ]);
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
                'validate' => 'required|max:255'
            ],
            [
                'name' => 'text',
                'type' => 'string',
                'validate' => 'required|max:500'
            ],
            [
                'name' => 'phone',
                'type' => 'string',
                'validate' => 'required|regex:/^[\d\-+ ]+$/i'
            ],
            [
                'name' => 'email',
                'type' => 'string',
                'validate' => 'required|email'
            ],
            [
                'name' => 'logo',
                'type' => 'file'
            ],
            [
                'name' => 'background',
                'type' => 'file'
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
        $validateRules = array_merge(
            $validateRules,
            ['data.theme.colors.*' => 'nullable|regex:/(#[a-f0-9]{3,6})/i'],
            ['alias' => 'nullable|max:150|regex:/[a-z0-9\-_]+/i']
        );

        $request->validate($validateRules);
    }

    public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
    {
        $content = [];
        $oldAttachIds = $this->getAttachIds($this->data);

        foreach ($this->getContentFields() as $field) {
            $content[$field['name']] = $request->input('data.content.' . $field['name']);
        }

        $this->data->put('content', $content);
        $newAttachIds = $this->getAttachIds($this->data);

        if ($oldAttachIds) {
            self::$deleteAttachIds = array_diff($oldAttachIds, array_values($newAttachIds));
        }

        $themeFields = $this->getThemeFields();
        $theme = [];

        if (!$themeFields->has('colors')) {
            return;
        }

        $colors = resolve(PageService::class)->prepareComponentColorsFromRequest(
            $themeFields->get('colors'),
            $request->input('data.theme.colors')
        );

        if (!$colors) {
            return;
        }

        /** @var \Kelnik\Core\Theme\Contracts\Color $color */
        foreach ($colors as $color) {
            if (!$color->isDifferentFromDefault() || !$color->getValue()) {
                continue;
            }

            $theme['colors'][$color->getFullName()] = $color->getValue();
        }

        $this->data->put('theme', $theme);
    }

    public function setDefaultValue(): void
    {
        $this->data = new Collection([
            'content' => [
                'title' => $this->getComponentTitleOriginal()
            ]
        ]);
    }

    protected function getAttachIds(Collection $originData): array
    {
        return array_filter([
            (int)Arr::get($originData, 'content.logo'),
            (int)Arr::get($originData, 'content.background')
        ]);
    }
}
